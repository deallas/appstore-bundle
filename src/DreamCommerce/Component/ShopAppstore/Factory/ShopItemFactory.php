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
use DreamCommerce\Component\ShopAppstore\Model\ShopItem;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopItemInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShopItemFactory extends AbstractFactory implements ShopItemFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new ShopItem();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiRequest(ShopInterface $shop, ItemResourceInterface $resource,
                                       RequestInterface $request, ResponseInterface $response): ShopItemInterface
    {
        return $this->createByShopAndData($shop, $resource, $this->handleApiRequest($request, $response));
    }

    /**
     * {@inheritdoc}
     */
    public function createByShopAndData(ShopInterface $shop, ItemResourceInterface $resource, array $data): ShopItemInterface
    {
        $fieldName = $resource->getExternalIdName();

        /** @var ShopItemInterface $item */
        $item = $this->createFromArray($data, $this->createNew());
        $item->setShop($shop);
        $item->setExternalId((int)$item->getFieldValue($fieldName));

        return $item;
    }
}