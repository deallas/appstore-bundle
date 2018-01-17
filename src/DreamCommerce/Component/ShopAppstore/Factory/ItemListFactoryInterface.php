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
use DreamCommerce\Component\ShopAppstore\Model\ItemListInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ItemListFactoryInterface extends FactoryInterface
{
    /**
     * @param ResourceInterface $resource
     * @param int $page
     * @param int $count
     * @param int $pages
     * @param string $identifierName
     * @param array $items
     * @return ItemListInterface
     */
    public function createByApiResponse(ResourceInterface $resource, int $page, int $count, int $pages, string $identifierName, array $items): ItemListInterface;
}