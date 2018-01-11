<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api\Http;

use DreamCommerce\Component\Common\Http\ClientInterface;
use DreamCommerce\Component\ShopAppstore\Api\Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class ShopClient implements ShopClientInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientInterface $httpClient
     * @param LoggerInterface|null $logger
     */
    public function __construct(ClientInterface $httpClient, LoggerInterface $logger = null)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri();
        $stream = $request->getBody();
        $body = $stream->getContents();
        $stream->rewind();

        $this->logger->debug(
            'Send request to "' . $uri->getHost() . '"',
            [
                'uri' => $uri->__toString(),
                'headers' => $request->getHeaders(),
                'body' => $body
            ]
        );

        try {
            $response = $this->httpClient->send($request);
        } catch (Throwable $exception) {
            if($exception instanceof RequestException) {
                $response = $exception->getResponse();
                $this->logResponse($response);

                $responseHeaders = $response->getHeaders();
                if ($response->getStatusCode() === 429 || !isset($responseHeaders['Retry-After'])) {
                    throw Exception\LimitExceededException::forExceededApiCalls(
                        $request,
                        $response,
                        isset($responseHeaders['Retry-After']) ? $responseHeaders['Retry-After'] : null,
                        $exception
                    );
                } else {
                    throw Exception\CommunicationException::forInvalidResponseCode($request, $response, $exception);
                }
            } else {
                throw Exception\CommunicationException::forBrokenConnection($request, $exception);
            }
        }

        $this->logResponse($response);
        $this->checkResponse($request, $response);

        return $response;
    }

    /**
     * @param ResponseInterface $response
     */
    private function logResponse(ResponseInterface $response): void
    {
        $stream = $response->getBody();
        $body = $stream->getContents();
        $stream->rewind();

        $this->logger->debug(
            'Received a response',
            [
                'status_code' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $body
            ]
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws Exception\CommunicationException
     * @throws Exception\MethodUnsupportedException
     * @throws Exception\NotFoundException
     * @throws Exception\ObjectLockedException
     * @throws Exception\PermissionsException
     * @throws Exception\ValidationException
     */
    private function checkResponse(RequestInterface $request, ResponseInterface $response): void
    {
        $responseCode = $response->getStatusCode();

        switch ($responseCode) {
            case 400:
                throw Exception\ValidationException::forResponse($request, $response);
            case 401:
                throw Exception\PermissionsException::forResponse($request, $response);
            case 404:
                throw Exception\NotFoundException::forResponse($request, $response);
            case 405:
                throw Exception\MethodUnsupportedException::forResponse($request, $response);
            case 409:
                throw Exception\ObjectLockedException::forResponse($request, $response);
        }

        if($responseCode !== 200) {
            throw Exception\CommunicationException::forInvalidResponseCode($request, $response);
        }
    }
}