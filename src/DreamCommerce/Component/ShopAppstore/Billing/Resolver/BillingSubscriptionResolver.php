<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\BillingSubscription;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Model\SubscriptionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class BillingSubscriptionResolver implements MessageResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $subscriptionFactory;

    public function resolve(Message $message)
    {
        Assert::isInstanceOf($message, BillingSubscription::class);

        $shop = $message->getShop();

        /** @var SubscriptionInterface $subscription */
        $subscription = $this->subscriptionFactory->createNew();
        $subscription->setExpiresAt($expiresAt);
        $subscription->setShop($shop);

        $this->objectManager->persist($subscription);
        $this->objectManager->flush();
    }
}