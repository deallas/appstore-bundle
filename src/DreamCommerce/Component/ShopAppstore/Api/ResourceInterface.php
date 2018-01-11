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

use ArrayObject;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

interface ResourceInterface
{
    /**
     * @param ShopInterface $shop
     * @param array ...$args
     * @return mixed
     */
    public function get(ShopInterface $shop, ...$args): ArrayObject;

    /**
     * @param ShopInterface $shop
     * @param array ...$args
     * @return ArrayObject
     */
    public function head(ShopInterface $shop, ...$args): ArrayObject;

    /**
     * @param ShopInterface $shop
     * @param array $data
     * @return int
     */
    public function post(ShopInterface $shop, array $data): int;

    /**
     * @param ShopInterface $shop
     * @param int $id
     * @param array $data
     * @return void
     */
    public function put(ShopInterface $shop, int $id, array $data): void;

    /**
     * @param ShopInterface $shop
     * @param int $id
     */
    public function delete(ShopInterface $shop, int $id): void;

    /**
     * @return string
     */
    public function getName(): string;
}