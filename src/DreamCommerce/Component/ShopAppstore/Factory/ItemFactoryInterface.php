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

use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ItemFactoryInterface extends FactoryInterface
{
    /**
     * @param ShopInterface $shop
     * @param int|null $externalId
     * @return ItemInterface
     */
    public function createByExternalIdAndShop(ShopInterface $shop, ?int $externalId): ItemInterface;

    /**
     * @param ShopInterface $shop
     * @param ResourceInterface $resource
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ItemInterface
     */
    public function createByApiRequest(ShopInterface $shop, ResourceInterface $resource,
                                       RequestInterface $request, ResponseInterface $response): ItemInterface;
}