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
use DreamCommerce\Component\ShopAppstore\Model\ItemListInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemPartListInterface;
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
     * @return ItemListInterface|ItemInterface[]
     */
    public function findBy(ShopInterface $shop, Criteria $criteria): ItemListInterface;

    /**
     * @param ShopInterface $shop
     * @param Criteria $criteria
     * @return ItemPartListInterface|ItemInterface[]
     */
    public function findByPartial(ShopInterface $shop, Criteria $criteria): ItemPartListInterface;

    /**
     * @param ShopInterface $shop
     * @return ItemListInterface|ItemInterface[]
     */
    public function findAll(ShopInterface $shop): ItemListInterface;

    /**
     * @param ShopInterface $shop
     * @param callable $callback
     * @param Criteria|null $criteria
     */
    public function walk(ShopInterface $shop, callable $callback, Criteria $criteria = null): void;

    /**
     * @param ItemInterface $item
     */
    public function reconnect(ItemInterface $item): void;

    /**
     * @param ShopInterface $shop
     * @param array $data
     * @return int
     */
    public function insert(ShopInterface $shop, array $data): int;

    /**
     * @param ShopInterface $shop
     * @param int $id
     * @param array $data
     */
    public function update(ShopInterface $shop, int $id, array $data): void;

    /**
     * @param ShopInterface $shop
     * @param int $id
     */
    public function delete(ShopInterface $shop, int $id): void;

    /**
     * @param ItemInterface $item
     */
    public function insertItem(ItemInterface $item): void;

    /**
     * @param ItemInterface $item
     */
    public function updateItem(ItemInterface $item): void;

    /**
     * @param ItemInterface $item
     */
    public function deleteItem(ItemInterface $item): void;
}