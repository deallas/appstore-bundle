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

namespace DreamCommerce\Component\ShopAppstore\Api\Resource;

use DreamCommerce\Component\ShopAppstore\Api\Criteria;
use DreamCommerce\Component\ShopAppstore\Api\ItemResource;
use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopItemInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopItemListInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopItemPartListInterface;
use Psr\Http\Message\UriInterface;

final class Metafield extends ItemResource
{
    /**
     * @var string|null
     */
    private $urlPart;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'metafields';
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalIdName(): string
    {
        return 'metafield_id';
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName(): string
    {
        return 'system';
    }

    /**
     * {@inheritdoc}
     */
    public function findByResource(ShopInterface $shop, ResourceInterface $resource, Criteria $criteria = null): ShopItemListInterface
    {
        $criteria = clone $criteria;
        $criteria->andWhere('object', $resource);

        return $this->findBy($shop, $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPartial(ShopInterface $shop, Criteria $criteria): ShopItemPartListInterface
    {
        $where = $criteria->getWhereExpression();
        if(isset($where['object']) && isset($where['object']['='])) {
            $this->urlPart = $where['object']['='];
        }

        return parent::findByPartial($shop, $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function insert(ShopInterface $shop, array $data): ShopItemInterface
    {
        if(isset($data['object'])) {
            $this->urlPart = $data['object'];
        }

        return parent::insert($shop, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(ShopInterface $shop, int $id, array $data): void
    {
        if(isset($data['object'])) {
            $this->urlPart = $data['object'];
        }

        parent::update($shop, $id, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUri(ShopInterface $shop, int $id = null): UriInterface
    {
        $uri = parent::getUri($shop, $id);
        $uri = $uri->withPath($uri->getPath() . '/' . ($this->urlPart === null) ? $this->getObjectName() : $this->urlPart);
        $this->urlPart = null;

        return $uri;
    }
}