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

use DreamCommerce\Component\ShopAppstore\Model\Item;
use DreamCommerce\Component\ShopAppstore\Model\ItemInterface;

final class ItemFactory implements ItemFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new Item();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiResponse(int $identifier, array $data): ItemInterface
    {
        $item = $this->createNew();
        $item->setIdentifier($identifier);

        foreach($data as $k => $v) {
            $item->$k = $v;
        }

        return $item;
    }
}