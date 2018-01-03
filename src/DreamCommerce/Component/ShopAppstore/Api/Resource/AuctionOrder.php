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

class AuctionOrder extends Resource
{
    /**
     * The order has already been connected to the auction
     */
    const HTTP_ERROR_AUCTION_ORDER_ALREADY_CONNECTED = 'auction_order_already_connected';

    protected $name = 'auction-orders';
}