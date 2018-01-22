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
    public function createByExternalIdAndShop(ShopInterface $shop, ?int $externalId): ItemInterface
    {
        $item = $this->createNew();
        $item->setShop($shop);
        $item->setExternalId($externalId);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiRequest(ShopInterface $shop, ResourceInterface $resource,
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

        $externalId = null;
        if($resource instanceof IdentifierAwareInterface) {
            $primaryKey = $resource->getIdentifierName();
            if(!isset($body[$primaryKey])) {
                throw CommunicationException::forInvalidResponseBody($request, $response);
            }
            $externalId = (int)$body[$primaryKey];
        }

        $item = $this->createByExternalIdAndShop($shop, $externalId);
        $item->setData($body);

        return $item;
    }
}