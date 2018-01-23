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

use DreamCommerce\Component\ShopAppstore\Api\Exception\CommunicationException;
use DreamCommerce\Component\ShopAppstore\Api\Resource\IdentifierAwareInterface;
use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;
use DreamCommerce\Component\ShopAppstore\Model\Item;
use DreamCommerce\Component\ShopAppstore\Model\ItemInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ItemFactory extends DataContainerFactory implements ItemFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new Item();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiRequest(ShopInterface $shop, ResourceInterface $resource,
                                       RequestInterface $request, ResponseInterface $response): ItemInterface
    {
        $fieldName = $resource->getE();

        /** @var ItemInterface $item */
        $item = parent::createByApiRequest($shop, $resource, $request, $response);
        $externalId = $item->getFieldValue($fieldName);
        if(!$externalId) {
            throw CommunicationException::forInvalidResponseBody($request, $response);
        }
        $item->setExternalId($externalId);

        return $item;
    }
}