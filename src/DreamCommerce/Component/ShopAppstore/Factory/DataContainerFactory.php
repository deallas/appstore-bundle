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
use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;
use DreamCommerce\Component\ShopAppstore\Model\DataContainer;
use DreamCommerce\Component\ShopAppstore\Model\DataContainerInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DataContainerFactory implements DataContainerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new DataContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiRequest(ShopInterface $shop, ResourceInterface $resource,
                                       RequestInterface $request, ResponseInterface $response): DataContainerInterface
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

        $item = $this->createFromArray($body);
        $item->setShop($shop);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data): DataContainerInterface
    {
        $container = $this->createNew();
        foreach($data as $k => $v) {
            if(is_array($v)) {
                $container[$k] = $this->createFromArray($v);
            } elseif(is_scalar($v) || is_null($v)) {
                $container[$k] = $v;
            } else {
                // TODO throw exception
            }
        }

        return $container;
    }
}