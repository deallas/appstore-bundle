<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

final class Uninstall extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return Message::ACTION_UNINSTALL;
    }
}