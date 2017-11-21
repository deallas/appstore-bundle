<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface BillingInterface extends ResourceInterface
{
    /**
     * @param ShopInterface|null $shop
     */
    public function setShop(?ShopInterface $shop): void;

    /**
     * @return ShopInterface|null
     */
    public function getShop(): ?ShopInterface;
}