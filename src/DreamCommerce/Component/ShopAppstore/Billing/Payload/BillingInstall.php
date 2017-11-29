<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

final class BillingInstall extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return Message::ACTION_BILLING_INSTALL;
    }
}