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

use DreamCommerce\Component\Common\Http\ClientInterface as HttpClientInterface;
use DreamCommerce\Component\Common\Http\LoggerInterface as HttpLoggerInterface;
use DreamCommerce\Component\ShopAppstore\Api\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ShopClient implements ShopClientInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var HttpLoggerInterface
     */
    private $httpLogger;

    /**
     * @param HttpClientInterface $httpClient
     * @param HttpLoggerInterface|null $httpLogger
     */
    public function __construct(HttpClientInterface $httpClient, HttpLoggerInterface $httpLogger = null)
    {
        $this->httpClient = $httpClient;
        $this->httpLogger = $httpLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        if($this->httpLogger !== null) {
            $this->httpLogger->logRequest($request);
        }

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

        if($this->httpLogger !== null) {
            $this->httpLogger->logResponse($response);
        }
        $this->checkResponse($request, $response, $exception);

        return $response;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
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