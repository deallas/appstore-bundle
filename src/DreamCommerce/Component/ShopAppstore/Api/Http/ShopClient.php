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
        $this->logRequest($request);
        $exception = null;

        try {
            $response = $this->httpClient->send($request);
        } catch (Throwable $exception) {
            if (class_exists('\\GuzzleHttp\\Exception\\RequestException') &&
                $exception instanceof \GuzzleHttp\Exception\RequestException
            ) {
                $response = $exception->getResponse();
            } else {
                throw Exception\CommunicationException::forBrokenConnection($request, $exception);
            }
        }

        $this->logResponse($response);
        $this->checkResponse($request, $response, $exception);

        return $response;
    }

    /**
     * @param RequestInterface $request
     */
    private function logRequest(RequestInterface $request): void
    {
        if($this->logger === null) {
            return;
        }

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
    }

    /**
     * @param ResponseInterface $response
     */
    private function logResponse(ResponseInterface $response): void
    {
        if($this->logger === null) {
            return;
        }

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
     * @param Throwable $previous
     * @throws Exception\CommunicationException
     * @throws Exception\MethodUnsupportedException
     * @throws Exception\NotFoundException
     * @throws Exception\ObjectLockedException
     * @throws Exception\PermissionsException
     * @throws Exception\ValidationException
     * @throws Exception\LimitExceededException
     */
    private function checkResponse(RequestInterface $request, ResponseInterface $response, Throwable $previous = null): void
    {
        $responseCode = $response->getStatusCode();

        switch ($responseCode) {
            case 400:
                throw Exception\ValidationException::forResponse($request, $response, $previous);
            case 401:
                throw Exception\PermissionsException::forResponse($request, $response, $previous);
            case 404:
                throw Exception\NotFoundException::forResponse($request, $response, $previous);
            case 405:
                throw Exception\MethodUnsupportedException::forResponse($request, $response, $previous);
            case 409:
                throw Exception\ObjectLockedException::forResponse($request, $response, $previous);
            case 429:
                throw Exception\LimitExceededException::forResponse($request, $response, $previous);
        }

        if($responseCode !== 200) {
            throw Exception\CommunicationException::forInvalidResponseCode($request, $response);
        }
    }
}