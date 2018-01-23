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
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface DataContainerFactoryInterface extends FactoryInterface
{
    /**
     * @param ShopInterface $shop
     * @param ResourceInterface $resource
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return DataContainerInterface
     */
    public function createByApiRequest(ShopInterface $shop, ResourceInterface $resource,
                                       RequestInterface $request, ResponseInterface $response): DataContainerInterface;

    /**
     * @param array $data
     * @return DataContainerInterface
     */
    public function createFromArray(array $data): DataContainerInterface;
}