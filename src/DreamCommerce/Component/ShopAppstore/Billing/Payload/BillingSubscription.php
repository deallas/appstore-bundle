<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

use DateTime;

final class BillingSubscription extends Message
{
    /**
     * @var DateTime
     */
    private $subscriptionEndTime;

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return Message::ACTION_BILLING_SUBSCRIPTION;
    }

    /**
     * @return DateTime
     */
    public function getSubscriptionEndTime(): DateTime
    {
        return $this->subscriptionEndTime;
    }
}