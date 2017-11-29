<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

trait ShopDependTrait
{
    /**
     * @var ShopInterface|null
     */
    protected $shop;

    /**
     * @return ShopInterface|null
     */
    public function getShop(): ?ShopInterface
    {
        return $this->shop;
    }

    /**
     * @param ShopInterface|null $shop
     */
    public function setShop(?ShopInterface $shop): void
    {
        $this->shop = $shop;
    }
}