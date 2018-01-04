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

namespace DreamCommerce\Component\ShopAppstore\Api;

use DreamCommerce\Component\Common\Http\GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class AwaitGuzzleClient extends GuzzleClient
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $retryLimit = 5;

    /**
     * @param GuzzleClientInterface $guzzleClient
     * @param null|LoggerInterface $logger
     */
    public function __construct(GuzzleClientInterface $guzzleClient, LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        parent::__construct($guzzleClient);
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request, array $options = array()): ResponseInterface
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
                'body' => $body,
                'options' => $options
            ]
        );

        return $this->perform(function() use($request, $options) {
            return parent::send($request, $options);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, $uri, array $options = array()): ResponseInterface
    {
        $body = isset($options['body']) ? $options['body'] : null;
        $headers = isset($options['headers']) ? $options['headers'] : null;

        $this->logger->debug(
            'Send request to "' . $uri . '"',
            [
                'uri' => $uri,
                'headers' => $headers,
                'body' => $body
            ]
        );

        return $this->perform(function() use($method, $uri, $options) {
            return parent::request($method, $uri, $options);
        });
    }

    /**
     * @return int
     */
    public function getRetryLimit(): int
    {
        return $this->retryLimit;
    }

    /**
     * @param int $retryLimit
     * @return void
     */
    public function setRetryLimit(int $retryLimit): void
    {
        $this->retryLimit = $retryLimit;
    }

    /**
     * @param callable $func
     * @return ResponseInterface
     */
    private function perform(callable $func): ResponseInterface
    {
        $responseHeaders = [];
        $counter = $this->retryLimit;

        /** @var ResponseInterface $response */
        $response = null;

        while($counter--) {
            try {
                // pause upon limits exceeding
                if (isset($responseHeaders['X-Shop-Api-Calls'])) {

                    $calls = $responseHeaders['X-Shop-Api-Calls'];
                    $limit = $responseHeaders['X-Shop-Api-Limit'];

                    if ($limit - $calls == 0) {
                        sleep(1);
                    }
                }

                $response = call_user_func($func);
            } catch (BadResponseException $exception) {
                $response = $exception->getResponse();
                if($response !== null) {
                    $this->logResponse($response);

                    $responseHeaders = $response->getHeaders();
                    if ($response->getStatusCode() === 429 || !isset($responseHeaders['Retry-After'])) {
                        sleep($responseHeaders['Retry-After']);
                    } else {
                        // TODO throw exception
                    }
                } else {
                    // TODO throw exception
                }
            }
        }

        if($response !== null) {
            $this->logResponse($response);
            if($response->getStatusCode() !== 200) {
                // TODO throw exception
            }
        } else {
            // TODO throw exception
        }

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
}