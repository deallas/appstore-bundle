<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Uninstall;
use Webmozart\Assert\Assert;

final class UninstallResolver implements MessageResolverInterface
{
    /**
     * @var ObjectManager
     */
    private $shopObjectManager;

    public function __construct(ObjectManager $shopObjectManager)
    {
        $this->shopObjectManager = $shopObjectManager;
    }

    public function resolve(Message $message)
    {
        Assert::isInstanceOf($message, Uninstall::class);

        $shop = $message->getShop();
        $shop->setInstalled(false);

        $this->shopObjectManager->persist($shop);
        $this->shopObjectManager->flush();
    }
}