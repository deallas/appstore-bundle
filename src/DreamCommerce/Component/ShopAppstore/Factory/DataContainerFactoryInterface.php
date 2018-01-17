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
use DreamCommerce\Component\ShopAppstore\Model\DataContainerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface DataContainerFactoryInterface extends FactoryInterface
{
    /**
     * @param ResourceInterface $resource
     * @param array $data
     * @return DataContainerInterface
     */
    public function createByApiResponse(ResourceInterface $resource, array $data): DataContainerInterface;
}