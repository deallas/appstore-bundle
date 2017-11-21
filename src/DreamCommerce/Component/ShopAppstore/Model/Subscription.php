<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;

class Subscription implements SubscriptionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var DateTime
     */
    protected $expiresAt;

    /**
     * @var ShopInterface
     */
    protected $shop;

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
    public function setExpiresAt(?DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
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
