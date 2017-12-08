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

namespace DreamCommerce\Component\ShopAppstore\Tests\Fixtures\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;

final class ExampleResolver implements MessageResolverInterface
{
    public function resolve(Message $message): void
    {
    }
}
