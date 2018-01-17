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
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ItemFactoryInterface extends FactoryInterface
{
    /**
     * @param ResourceInterface $resource
     * @param int $identifier
     * @param array $data
     * @return ItemInterface
     */
    public function createByApiResponse(ResourceInterface $resource, int $identifier, array $data): ItemInterface;
}