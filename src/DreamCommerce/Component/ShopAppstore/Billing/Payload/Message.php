<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

use DateTime;
use DateTimeZone;
use DreamCommerce\Component\Common\Model\ArrayableInterface;
use DreamCommerce\Component\Common\Model\ArrayableTrait;
use DreamCommerce\Component\ShopAppstore\Billing\DispatcherInterface;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

abstract class Message implements ArrayableInterface
{
    use ArrayableTrait;

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
    protected $timestamp;

    /**
     * @param ApplicationInterface $application
     * @param ShopInterface $shop
     * @param array $params
     */
    public function __construct(ApplicationInterface $application, ShopInterface $shop, array $params = array())
    {
        $this->fromArray($params);

        $this->application = $application;
        $this->shop = $shop;
    }

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
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        if(!($timestamp instanceof DateTime)) {
            $timestamp = new DateTime($timestamp, new DateTimeZone(DispatcherInterface::TIMEZONE));
        }

        $this->timestamp = $timestamp;
    }
}