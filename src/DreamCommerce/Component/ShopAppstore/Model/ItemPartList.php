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

namespace DreamCommerce\Component\ShopAppstore\Model;

final class ItemPartList extends ItemList
{
    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $totalPages;

    /**
     * @param array $items
     * @param int $total
     * @param int $page
     * @param int $totalPages
     */
    public function __construct(array $items, int $total, int $page, int $totalPages)
    {
        $this->total = $total;
        $this->page = $page;
        $this->totalPages = $totalPages;

        parent::__construct($items);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
}