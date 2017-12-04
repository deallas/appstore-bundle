<?php

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