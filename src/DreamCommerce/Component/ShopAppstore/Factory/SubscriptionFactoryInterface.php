<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Factory;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\BillingSubscription;
use DreamCommerce\Component\ShopAppstore\Model\SubscriptionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface SubscriptionFactoryInterface extends FactoryInterface
{
    /**
     * @param BillingSubscription $billingSubscription
     * @return SubscriptionInterface
     */
    public function createNewByPayload(BillingSubscription $billingSubscription): SubscriptionInterface;
}