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

use DreamCommerce\Component\ShopAppstore\Model\ItemList;
use DreamCommerce\Component\ShopAppstore\Model\ItemListInterface;

final class ItemListFactory implements ItemListFactoryInterface
{
    /**
     * @var ItemFactoryInterface
     */
    private $itemFactory;

    /**
     * @param ItemFactoryInterface $itemFactory
     */
    public function __construct(ItemFactoryInterface $itemFactory)
    {
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new ItemList();
    }

    /**
     * {@inheritdoc}
     */
    public function createByApiResponse(int $page, int $count, int $pages, string $identifierName, array $items): ItemListInterface
    {
        $list = $this->createNew();
        $list->setPage($page);
        $list->setCount($count);
        $list->setPageCount($pages);

        $rows = [];


        $list->setItems($items);

        return $list;
    }
}