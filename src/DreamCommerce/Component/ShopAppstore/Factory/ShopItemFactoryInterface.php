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

namespace DreamCommerce\Component\ShopAppstore\Factory;

use DreamCommerce\Component\ShopAppstore\Api\ItemResourceInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopItemInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ShopItemFactoryInterface extends FactoryInterface
{
    /**
     * @param ItemResourceInterface $resource
     * @return ShopItemInterface
     */
    public function createByApiResource(ItemResourceInterface $resource): ShopItemInterface;

    /**
     * @param ItemResourceInterface $resource
     * @param ShopInterface $shop
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ShopItemInterface
     */
    public function createByApiRequest(ItemResourceInterface $resource, ShopInterface $shop,
                                       RequestInterface $request, ResponseInterface $response): ShopItemInterface;

    /**
     * @param ItemResourceInterface $resource
     * @param ShopInterface $shop
     * @param array $data
     * @return ShopItemInterface
     */
    public function createByShopAndData(ItemResourceInterface $resource, ShopInterface $shop, array $data): ShopItemInterface;
}