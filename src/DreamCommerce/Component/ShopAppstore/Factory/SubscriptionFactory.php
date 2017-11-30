<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Factory;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\BillingSubscription;
use DreamCommerce\Component\ShopAppstore\Model\SubscriptionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class SubscriptionFactory implements SubscriptionFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    /**
     * {@inheritdoc}
     */
    public function createNew(): SubscriptionInterface
    {
        return $this->factory->createNew();
    }

    /**
     * @param BillingSubscription $billingSubscription
     * @return SubscriptionInterface
     */
    public function createNewByPayload(BillingSubscription $billingSubscription): SubscriptionInterface
    {
        $object = $this->createNew();
        $object->setShop($billingSubscription->getShop());
        $object->setExpiresAt($billingSubscription->getSubscriptionEndTime());

        return $object;
    }
}