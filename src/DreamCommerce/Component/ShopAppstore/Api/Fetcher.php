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

use DreamCommerce\Component\ShopAppstore\Model\ItemListInterface;

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
     * @param Criteria|null $criteria
     * @return ItemListInterface
     */
    public function fetchAll(Criteria $criteria = null): ItemListInterface
    {
        // TODO
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
     * @param Criteria $criteria
     */
    public function walk(callable $callback, Criteria $criteria = null): void
    {
        // TODO
    }
}