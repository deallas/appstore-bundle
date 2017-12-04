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
     * @param ApplicationInterface $application
     * @param ShopInterface $shop
     * @param DateTime $dateTime
     * @param array $params
     */
    public function __construct(ApplicationInterface $application, ShopInterface $shop, DateTime $dateTime, array $params = array())
    {
        $this->fromArray($params);

        $this->application = $application;
        $this->shop = $shop;
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