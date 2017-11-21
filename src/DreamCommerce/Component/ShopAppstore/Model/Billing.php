<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Billing implements BillingInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var ShopInterface
     */
    protected $shop;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setShop(?ShopInterface $shop): void
    {
        $this->shop = $shop;
    }

    /**
     * {@inheritdoc}
     */
    public function getShop(): ?ShopInterface
    {
        return $this->shop;
    }
}
