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

use DreamCommerce\Component\ShopAppstore\Model\ShopData;
use DreamCommerce\Component\ShopAppstore\Model\ShopDataInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShopDataFactory extends AbstractFactory implements ShopDataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new ShopData();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiRequest(ShopInterface $shop, RequestInterface $request, ResponseInterface $response): ShopDataInterface
    {
        /** @var ShopDataInterface $data */
        $data = $this->createFromArray($this->handleApiRequest($request, $response), $this->createNew());
        $data->setShop($shop);

        return $data;
    }
}