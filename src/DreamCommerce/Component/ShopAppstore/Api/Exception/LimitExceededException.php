<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class LimitExceededException extends ApiException
{
    const CODE_EXCEEDED_API_CALLS           = 10;
    const CODE_EXCEEDED_MAX_API_RETRIES     = 11;

    /**
     * @var int|null
     */
    private $retryAfter;

    /**
     * @var int|null
     */
    private $maxAttempts;

    /**
     * @param RequestInterface $httpRequest
     * @param ResponseInterface $httpResponse
     * @param int|null $retryAfter
     * @param Throwable|null $previous
     * @return LimitExceededException
     */
    public static function forExceededApiCalls(RequestInterface $httpRequest, ResponseInterface $httpResponse, ?int $retryAfter, Throwable $previous = null): self
    {
        $exception = new self('The API calls has been exceeded', self::CODE_EXCEEDED_API_CALLS, $previous);
        $exception->retryAfter = $retryAfter;
        $exception->httpRequest = $httpRequest;
        $exception->httpResponse = $httpResponse;

        return $exception;
    }

    /**
     * @param RequestInterface $httpRequest
     * @param int|null $maxAttempts
     * @param Throwable|null $previous
     * @return LimitExceededException
     */
    public static function forExceededMaxApiRetries(RequestInterface $httpRequest, ?int $maxAttempts, Throwable $previous = null): self
    {
        $exception = new self('The maximum number of attempts to retry HTTP query has been exceeded', self::CODE_EXCEEDED_MAX_API_RETRIES, $previous);
        $exception->httpRequest = $httpRequest;
        $exception->maxAttempts = $maxAttempts;

        return $exception;
    }

    /**
     * @return int|null
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    /**
     * @return int|null
     */
    public function getMaxAttempts(): ?int
    {
        return $this->maxAttempts;
    }
}