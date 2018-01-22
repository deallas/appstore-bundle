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

final class ItemList extends AbstractItemList implements ItemListInterface
{
    /**
     * {@inheritdoc}
     */
    public function addPart(ItemPartList $itemPartList): void
    {
        $this->items = array_merge($this->items, $itemPartList->items);
        $this->count = count($this->items);
    }
}