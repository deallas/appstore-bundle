<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Exception\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Exception\BillingException;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class UnableDispatchException extends BillingException
{
    const CODE_INVALID_REQUEST_METHOD       = 10;
    const CODE_INVALID_PAYLOAD_HASH         = 11;
    const CODE_NOT_EXIST_APPLICATION        = 12;
    const CODE_UNFULFILLED_REQUIREMENTS     = 13;
    const CODE_NOT_SUPPORTED_ACTION         = 14;
    const CODE_UNSUPPORTED_SHOP_STATE       = 15;

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * @var ApplicationInterface
     */
    private $application;

    /**
     * @var ShopInterface
     */
    private $shop;

    /**
     * @var Message
     */
    private $payload;

    /**
     * @param ServerRequestInterface $serverRequest
     * @param null|Throwable $exception
     * @return UnableDispatchException
     */
    public static function forInvalidRequestMethod(ServerRequestInterface $serverRequest, Throwable $exception = null): UnableDispatchException
    {
        $exception = new static('Invalid request method', self::CODE_INVALID_REQUEST_METHOD, $exception);
        $exception->serverRequest = $serverRequest;

        return $exception;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param ApplicationInterface $application
     * @param Throwable|null $exception
     * @return UnableDispatchException
     */
    public static function forInvalidPayloadHash(ServerRequestInterface $serverRequest, ApplicationInterface $application, Throwable $exception = null): UnableDispatchException
    {
        $exception = new static('Invalid payload hash', self::CODE_INVALID_PAYLOAD_HASH, $exception);
        $exception->serverRequest = $serverRequest;
        $exception->application = $application;

        return $exception;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param Throwable|null $exception
     * @return UnableDispatchException
     */
    public static function forNotExistApplication(ServerRequestInterface $serverRequest, Throwable $exception = null): UnableDispatchException
    {
        $exception = new static('Application does not exist', self::CODE_NOT_EXIST_APPLICATION, $exception);
        $exception->serverRequest = $serverRequest;

        return $exception;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param Throwable|null $exception
     * @return UnableDispatchException
     */
    public static function forNotSupportedAction(ServerRequestInterface $serverRequest, Throwable $exception = null): UnableDispatchException
    {
        $exception = new static('Action is not supported', self::CODE_NOT_SUPPORTED_ACTION, $exception);
        $exception->serverRequest = $serverRequest;

        return $exception;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param Throwable|null $exception
     * @return UnableDispatchException
     */
    public static function forUnfulfilledRequirements(ServerRequestInterface $serverRequest, Throwable $exception = null): UnableDispatchException
    {
        $exception = new static('Requirements have not been met', self::CODE_UNFULFILLED_REQUIREMENTS, $exception);
        $exception->serverRequest = $serverRequest;

        return $exception;
    }

    /**
     * @param ShopInterface $shop
     * @param Message $payload
     * @param Throwable|null $exception
     * @return UnableDispatchException
     */
    public static function forUnsupportedShopState(ShopInterface $shop, Message $payload, Throwable $exception = null): UnableDispatchException
    {
        $exception = new static('Unsupported state of shop', self::CODE_UNSUPPORTED_SHOP_STATE, $exception);
        $exception->payload = $payload;
        $exception->shop = $shop;

        return $exception;
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function getServerRequest(): ?ServerRequestInterface
    {
        return $this->serverRequest;
    }

    /**
     * @return ApplicationInterface|null
     */
    public function getApplication(): ?ApplicationInterface
    {
        return $this->application;
    }

    /**
     * @return ShopInterface
     */
    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    /**
     * @return Message
     */
    public function getPayload(): Message
    {
        return $this->payload;
    }
}