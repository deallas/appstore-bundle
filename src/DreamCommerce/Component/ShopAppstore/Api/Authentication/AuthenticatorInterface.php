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

namespace DreamCommerce\Component\ShopAppstore\Api\Authentication;

use ArrayObject;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\TokenInterface;

interface AuthenticatorInterface
{
    /**
     * @param ShopInterface $shop
     * @param bool $force
     * @return TokenInterface
     */
    public function authenticate(ShopInterface $shop, bool $force = false): TokenInterface;
}