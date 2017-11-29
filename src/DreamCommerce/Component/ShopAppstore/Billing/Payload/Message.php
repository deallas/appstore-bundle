<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

use DateTime;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

abstract class Message
{
    public const ACTION_BILLING_INSTALL         = 'billing_install';
    public const ACTION_BILLING_SUBSCRIPTION    = 'billing_subscription';
    public const ACTION_INSTALL                 = 'install';
    public const ACTION_UPGRADE                 = 'upgrade';
    public const ACTION_UNINSTALL               = 'uninstall';

    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @return string
     */
    abstract public function getAction(): string;

    /**
     * @return ShopInterface
     */
    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    /**
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }
}