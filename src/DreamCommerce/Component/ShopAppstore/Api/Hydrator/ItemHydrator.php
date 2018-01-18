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

namespace DreamCommerce\Component\ShopAppstore\Api\Hydrator;

use DreamCommerce\Component\ShopAppstore\Api\Exception;
use DreamCommerce\Component\ShopAppstore\Api\Resource\IdentifierAwareInterface;
use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;
use DreamCommerce\Component\ShopAppstore\Factory\DataContainerFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ItemFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ItemListFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemList;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ItemHydrator implements HydratorInterface
{
    /**
     * @var DataContainerFactoryInterface
     */
    private $dataContainerFactory;

    /**
     * @var ItemFactoryInterface
     */
    private $itemFactory;

    /**
     * @var ItemListFactoryInterface
     */
    private $itemListFactory;

    /**
     * @param DataContainerFactoryInterface $dataContainerFactory
     * @param ItemFactoryInterface $itemFactory
     * @param ItemListFactoryInterface $itemListFactory
     */
    public function __construct(DataContainerFactoryInterface $dataContainerFactory = null,
                                ItemFactoryInterface $itemFactory = null,
                                ItemListFactoryInterface $itemListFactory = null
    ) {
        $this->dataContainerFactory = $dataContainerFactory;
        $this->itemFactory = $itemFactory;
        $this->itemListFactory = $itemListFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(ResourceInterface $resource, RequestInterface $request, ResponseInterface $response): void
    {
        $body = null;

        if($request->getMethod() !== 'HEAD') {
            $stream = $response->getBody();
            $stream->rewind();

            $body = $stream->getContents();
            if(strlen($body) === 0) {
                throw Exception\CommunicationException::forEmptyResponseBody($request, $response);
            }
            $body = @json_decode($body, true);

            if(!$body || !is_array($body)) {
                throw Exception\CommunicationException::forUnsupportedResponseBody($request, $response);
            }

            if(isset($body['list'])) {
                /** @var ItemList $result */
                $result = $this->itemListFactory->createNew();
                if(isset($body['page'])) {
                    $result->setPage((int)$body['page']);
                }
                if(isset($body['count'])) {
                    $result->setTotal((int)$body['count']);
                }
                if(isset($body['pages'])) {
                    $result->setTotalPages((int)$body['pages']);
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
                        $item->setIdentifier($identifier);
                        $items[$identifier] = $item;
                    } else {
                        $items[] = $item;
                    }
                }

                $result = $this->itemListFactory->createNew();
                $result->setItems($items);
            } else {
                $data = $this->dataContainerFactory->createNew();
                $data->setData();
            }
        } else {
            $result = $this->itemListFactory->createNew();
        }

        if($result instanceof ItemListInterface) {
            $headers = $response->getHeaders();
            if(isset($headers['X-Shop-Result-Page'])) {
                $result->setPage($headers['X-Shop-Result-Page']);
            }
            if(isset($headers['X-Shop-Result-Count'])) {
                $result->setTotal($headers['X-Shop-Result-Count']);
            }
            if(isset($headers['X-Shop-Result-Pages'])) {
                $result->setTotalPages($headers['X-Shop-Result-Pages']);
            }
        }

        return $result;
    }
}