<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api\Exception;

use DreamCommerce\Component\ShopAppstore\Exception\ShopAppstoreException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiException extends ShopAppstoreException
{
    const CODE_INVALID_RESPONSE     = 1;

    /**
     * @var RequestInterface
     */
    protected $httpRequest;

    /**
     * @var ResponseInterface
     */
    protected $httpResponse;

    /**
     * @return RequestInterface
     */
    public function getHttpRequest(): RequestInterface
    {
        return $this->httpRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getHttpResponse(): ResponseInterface
    {
        return $this->httpResponse;
    }
}