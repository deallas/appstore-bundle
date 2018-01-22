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
    public function createByApiRequest(ShopInterface $shop, ResourceInterface $resource,
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

        $page = 1;
        $total = 0;
        $totalPages = 0;

        if(isset($body['page'])) {
            $page = (int)$body['page'];
        }
        if(isset($body['count'])) {
            $total = (int)$body['count'];
        }
        if(isset($body['pages'])) {
            $totalPages = (int)$body['pages'];
        }

        if($resource instanceof IdentifierAwareInterface) {
            $factory = $this->itemFactory;
            $identifierName = $resource->getIdentifierName();
        } else {
            $factory = $this->dataContainerFactory;
            $identifierName = null;
        }

        $items = [];
        foreach($body['list'] as $data) {
            $item = $factory->createNew();
            $item->setData($data);

            if($identifierName !== null) {
                if(!isset($data[$identifierName])) {
                    // TODO throw
                }
                $item->setId((int)$data[$identifierName]);
                $items[] = $item;
            } else {
                $items[] = $item;
            }
        }

        return new ItemPartList($items, $total, $page, $totalPages);
    }
}