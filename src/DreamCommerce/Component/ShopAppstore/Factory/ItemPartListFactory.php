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
use DreamCommerce\Component\ShopAppstore\Api\ItemResourceInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemPartList;
use DreamCommerce\Component\ShopAppstore\Model\ItemPartListInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ItemPartListFactory implements ItemPartListFactoryInterface
{
    /**
     * @var ItemFactoryInterface
     */
    protected $itemFactory;

    /**
     * @param ItemFactoryInterface $itemFactory
     */
    public function __construct(ItemFactoryInterface $itemFactory)
    {
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new ItemPartList();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiRequest(ShopInterface $shop, ItemResourceInterface $resource,
                                       RequestInterface $request, ResponseInterface $response): ItemPartListInterface
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

        $itemPartList = $this->createNew();

        if(isset($body['page'])) {
            $itemPartList->setPage((int)$body['page']);
        }
        if(isset($body['count'])) {
            $itemPartList->setTotal((int)$body['count']);
        }
        if(isset($body['pages'])) {
            $itemPartList->setTotalPages((int)$body['pages']);
        }

        $externalIdName = $resource->getExternalIdName();

        $items = [];
        foreach($body['list'] as $data) {
            $item = $this->itemFactory->createFromArray($data);
            $item->setExternalId((int)$data[$externalIdName]);

            $items[] = $item;
        }
        $itemPartList->setItems($items);

        return $itemPartList;
    }
}