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
     * @return DateTime
     */
    public function getSubscriptionEndTime(): DateTime
    {
        return $this->subscriptionEndTime;
    }
}