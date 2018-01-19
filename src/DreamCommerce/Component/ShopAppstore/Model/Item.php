<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

class Item extends DataContainer implements ItemInterface
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var ShopInterface|null
     */
    private $shop;

    /**
     * @param ShopInterface|null $shop
     * @param int|null $id
     * @param array $data
     */
    public function __construct(ShopInterface $shop = null, int $id = null, array $data = [])
    {
        $this->shop = $shop;
        $this->id = $id;

        parent::__construct($data);
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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    /**
     * {@inheritdoc}
     */
    public function setShop(ShopInterface $shop): void
    {
        $this->shop = $shop;
    }
}