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
use DreamCommerce\Component\ShopAppstore\Api\Resource;
use DreamCommerce\Component\ShopAppstore\Api\Resource\IdentifierAwareInterface;
use DreamCommerce\Component\ShopAppstore\Model\Item;
use DreamCommerce\Component\ShopAppstore\Model\ItemInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ItemFactory implements ItemFactoryInterface
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
    public function createByApiRequest(ShopInterface $shop, Resource $resource,
                                       RequestInterface $request, ResponseInterface $response): ItemInterface
    {
        $stream = $response->getBody();
        $stream->rewind();

        $body = $stream->getContents();
        if(strlen($body) === 0) {
            throw CommunicationException::forEmptyResponseBody($request, $response);
        }
        $body = @json_decode($body, true);

        if(!$body || !is_array($body)) {
            throw CommunicationException::forInvalidResponseBody($request, $response);
        }

        $item = $this->createNew();
        if($resource instanceof IdentifierAwareInterface) {
            $primaryKey = $resource->getIdentifierName();
            if(!isset($body[$primaryKey])) {
                throw CommunicationException::forInvalidResponseBody($request, $response);
            }
            $item->setId((int)$body[$primaryKey]);
        }

        $item->setShop($shop);
        $item->setData($body);

        return $item;
    }
}