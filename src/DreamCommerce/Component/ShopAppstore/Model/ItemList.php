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

class ItemList implements ItemListInterface
{
    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $totalPages = 0;

    /**
     * @var ItemInterface[]
     */
    private $items;

    /**
     * @var int
     */
    private $pointer = 0;
}
