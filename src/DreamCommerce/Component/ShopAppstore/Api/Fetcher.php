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

namespace DreamCommerce\Component\ShopAppstore\Api;

use DreamCommerce\Component\ShopAppstore\Api\Exception\LimitExceededException;
use DreamCommerce\Component\ShopAppstore\Model\ItemList;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

final class Fetcher
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @param ResourceInterface $resource
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param ShopInterface $shop
     * @param Criteria|null $criteria
     * @return ItemList
     */
    public function fetchAll(ShopInterface $shop, Criteria $criteria = null): ItemList
    {
        if($criteria === null) {
            $criteria = Criteria::create();
        }
        $criteria->setPage(1);

        $itemList = new ItemList();
        $totalPages = null;

        do {
            try {
                $itemPartList = $this->resource->list($shop, $criteria);
            } catch(LimitExceededException $exception) {
                // TODO throw
            }

            $itemList->addPart($itemPartList);
            $criteria->nextPage();
        } while($criteria->getPage() <= $itemPartList->getTotalPages());

        return $itemList;
    }

    /**
     * @param ResourceInterface $resource
     * @param string|null $foreign
     * @param array $filters
     */
    public function connect(ResourceInterface $resource, string $foreign = null, $filters = []): void
    {
        // TODO
    }

    /**
     * @param callable $callback
     * @param ShopInterface $shop
     * @param Criteria|null $criteria
     */
    public function walk(callable $callback, ShopInterface $shop, Criteria $criteria = null): void
    {
        // TODO
    }
}