<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

interface ShopDependInterface
{
    /**
     * @return ShopInterface|null
     */
    public function getShop(): ?ShopInterface;

    /**
     * @param ShopInterface|null $shop
     */
    public function setShop(?ShopInterface $shop): void;
}