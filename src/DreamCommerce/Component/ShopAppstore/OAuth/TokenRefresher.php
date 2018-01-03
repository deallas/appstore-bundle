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

namespace DreamCommerce\Component\ShopAppstore\OAuth;

use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

final class TokenRefresher implements TokenRefresherInterface
{
    /**
     * {@inheritdoc}
     */
    public function refresh(ShopInterface $shop): void
    {

    }
}
