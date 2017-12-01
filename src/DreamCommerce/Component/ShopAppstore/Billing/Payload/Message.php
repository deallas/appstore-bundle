<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

use DateTime;
use DreamCommerce\Component\Common\Model\ArrayableInterface;
use DreamCommerce\Component\Common\Model\ArrayableTrait;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

abstract class Message implements ArrayableInterface
{
    use ArrayableTrait;

    /**
     * @var ShopInterface
     */
    private $shop;

    /**
     * @var ApplicationInterface
     */
    private $application;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param ShopInterface $shop
     * @param ApplicationInterface $application
     * @param DateTime $dateTime
     * @param array $params
     */
    public function __construct(ShopInterface $shop, ApplicationInterface $application, DateTime $dateTime, array $params = array())
    {
        $this->fromArray($params);

        $this->shop = $shop;
        $this->application = $application;
        $this->dateTime = $dateTime;
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
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }
}