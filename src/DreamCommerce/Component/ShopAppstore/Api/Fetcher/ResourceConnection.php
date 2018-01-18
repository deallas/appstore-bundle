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

namespace DreamCommerce\Component\ShopAppstore\Api\Fetcher;

use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;

final class ResourceConnection
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var string|null
     */
    private $foreignKey;

    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $filters;

    /**
     * @param ResourceInterface $resource
     * @param string|null $foreignKey if null - copies selfKey
     * @param array $filters
     */
    public function __construct(ResourceInterface $resource, string $foreignKey = null, array $filters = [])
    {
        $this->resource = $resource;
        $this->foreignKey = $foreignKey;

        $this->class = $this->transformClassName($resource);
        $this->filters = $filters;
    }

    /**
     * @return ResourceInterface
     */
    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getForeignKey(): ?string
    {
        if(empty($this->foreignKey)) {
            // TODO return $this->getSelfKey();
        }
        return $this->foreignKey;
    }

    /**
     * @param ResourceInterface $resource
     * @return string
     */
    private function transformClassName(ResourceInterface $resource): string
    {
        $key = get_class($resource);
        $key = substr($key, strrpos($key, '\\')+1);

        return $key;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

}