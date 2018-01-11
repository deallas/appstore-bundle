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

use DreamCommerce\Component\ShopAppstore\Api\Exception\LimitExceededException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AwaitShopClient extends ShopClient
{
    /**
     * @var int
     */
    private $retryLimit = 5;

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
     * {@inheritdoc}
     */
    public function send(RequestInterface $request): ResponseInterface
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

                $response = parent::send($request);
            } catch (LimitExceededException $exception) {
                if($exception->getCode() === LimitExceededException::CODE_EXCEEDED_API_CALLS) {
                    $response = $exception->getHttpResponse();
                    $responseHeaders = $response->getHeaders();

                    sleep($exception->getRetryAfter());
                } else {
                    throw $exception;
                }
            }
        }

        if($response === null) {
            throw LimitExceededException::forExceededMaxApiRetries($request, $this->retryLimit);
        }

        return $response;
    }
}