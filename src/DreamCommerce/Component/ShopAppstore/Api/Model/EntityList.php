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

namespace DreamCommerce\Component\ShopAppstore\Api\Model;

use ArrayObject;
use DreamCommerce\Component\ShopAppstore\Api\EntityListInterface;

final class EntityList extends ArrayObject implements EntityListInterface
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $pages = 0;

    /**
     * @param array $array
     * @param int $flags
     */
    public function __construct($array = [], $flags = parent::ARRAY_AS_PROPS)
    {
        parent::__construct($array, $flags);
    }

    /**
     * @param int|null $count
     */
    public function setCount(?int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int|null $page
     */
    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int|null $count
     */
    public function setPageCount(?int $count): void
    {
        $this->pages = $count;
    }

    /**
     * @return int|null
     */
    public function getPageCount(): ?int
    {
        return $this->pages;
    }
}
