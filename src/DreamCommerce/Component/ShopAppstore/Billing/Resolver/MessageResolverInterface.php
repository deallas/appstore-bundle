<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;

interface MessageResolverInterface
{
    public function resolve(Message $message);
}