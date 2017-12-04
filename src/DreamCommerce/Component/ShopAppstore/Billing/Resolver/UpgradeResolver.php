<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Upgrade;
use Webmozart\Assert\Assert;

final class UpgradeResolver implements MessageResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(Message $message): void
    {
        /** @var Upgrade $message */
        Assert::isInstanceOf($message, Upgrade::class);

        $shop = $message->getShop();
        $appVersion = $message->getApplicationVersion();

        if($appVersion > $shop->getVersion()) {
            $shop->setVersion($appVersion);
        }
    }
}