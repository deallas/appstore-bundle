<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

use DateTime;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

abstract class Message
{
    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @return ShopInterface
     */
    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    /**
     * @return ApplicationInterface
     */
    public function getApplication(): ApplicationInterface
    {
        return $this->application;
    }

    /**
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * @return array
     */
    public function getRequiredParams(): array
    {
        return [
            'shop', 'shop_url', 'application_code', 'hash', 'timestamp'
        ];
    }
}