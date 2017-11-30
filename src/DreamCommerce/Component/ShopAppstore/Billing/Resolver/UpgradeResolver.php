<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Upgrade;
use Webmozart\Assert\Assert;

final class UpgradeResolver implements MessageResolverInterface
{
    /**
     * @var ObjectManager
     */
    private $shopObjectManager;

    /**
     * @param ObjectManager $shopObjectManager
     */
    public function __construct(ObjectManager $shopObjectManager)
    {
        $this->shopObjectManager = $shopObjectManager;
    }

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

        $this->shopObjectManager->persist($shop);
        $this->shopObjectManager->flush();
    }
}