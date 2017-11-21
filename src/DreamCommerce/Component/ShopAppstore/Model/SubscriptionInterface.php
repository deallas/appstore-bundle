<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SubscriptionInterface extends ResourceInterface
{
    /**
     * @param DateTime|null $expiresAt
     */
    public function setExpiresAt(?DateTime $expiresAt): void;

    /**
     * @return DateTime
     */
    public function getExpiresAt(): ?DateTime;

    /**
     * @param ShopInterface $shop
     */
    public function setShop(?ShopInterface $shop): void;

    /**
     * @return ShopInterface
     */
    public function getShop(): ?ShopInterface;
}