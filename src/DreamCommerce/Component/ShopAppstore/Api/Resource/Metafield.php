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

use DreamCommerce\Component\ShopAppstore\Api\ItemResource;

final class Metafield extends ItemResource
{
    /**
     * type of integer
     */
    const TYPE_INT = 1;

    /**
     * type of float
     */
    const TYPE_FLOAT = 2;

    /**
     * type of string
     */

    const TYPE_STRING = 3;

    /**
     * type of binary data
     */
    const TYPE_BLOB = 4;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'metafields';
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalIdName(): string
    {
        return 'metafield_id';
    }
}