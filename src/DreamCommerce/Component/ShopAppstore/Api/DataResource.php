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

use DreamCommerce\Component\ShopAppstore\Model\DataContainerInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

abstract class DataResource extends Resource implements DataResourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetch(ShopInterface $shop): DataContainerInterface
    {
        // TODO
    }
}