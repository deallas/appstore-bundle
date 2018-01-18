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

namespace DreamCommerce\Component\ShopAppstore\Api;

use DreamCommerce\Component\ShopAppstore\Model\ItemInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemList;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

interface ResourceInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param ShopInterface $shop
     * @param int $id
     * @return ItemInterface
     */
    public function find(ShopInterface $shop, int $id): ItemInterface;

    /**
     * @param ShopInterface $shop
     * @param Criteria $criteria
     * @return ItemList
     */
    public function findBy(ShopInterface $shop, Criteria $criteria): ItemList;

    /**
     * @param ShopInterface $shop
     * @return ItemList
     */
    public function findAll(ShopInterface $shop): ItemList;

    /**
     * @param ShopInterface $shop
     * @param array $data
     * @return ItemInterface
     */
    public function insert(ShopInterface $shop, $data): ItemInterface;

    /**
     * @param ShopInterface $shop
     * @param int $id
     * @param array $data
     */
    public function update(ShopInterface $shop, int $id, array $data): void;

    /**
     * @param ItemInterface $item
     */
    public function updateItem(ItemInterface $item): void;

    /**
     * @param ShopInterface $shop
     * @param int $id
     */
    public function delete(ShopInterface $shop, int $id): void;

    /**
     * @param ItemInterface $item
     */
    public function deleteItem(ItemInterface $item): void;
}