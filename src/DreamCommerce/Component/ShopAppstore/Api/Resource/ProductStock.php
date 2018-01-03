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

use DreamCommerce\Component\ShopAppstore\Api\Resource;

class ProductStock extends Resource
{
    /**
     * keep base price
     */
    const PRICE_TYPE_KEEP = 0;

    /**
     * specify price for stock
     */
    const PRICE_TYPE_NEW = 1;

    /**
     * increase base price
     */
    const PRICE_TYPE_INCREASE = 2;

    /**
     * dreacrease base price
     */
    const PRICE_TYPE_DECREASE = 3;

    /**
     * keep base weight
     */
    const WEIGHT_TYPE_KEEP = 0;

    /**
     * specify weight for stock
     */
    const WEIGHT_TYPE_NEW = 1;

    /**
     * increase base weight
     */
    const WEIGHT_TYPE_INCREASE = 2;

    /**
     * decrease base weight
     */
    const WEIGHT_TYPE_DECREASE = 3;

    protected $name = 'product-stocks';
}