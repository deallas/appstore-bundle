<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

use DateTime;
use DateTimeZone;
use DreamCommerce\Component\ShopAppstore\Billing\DispatcherInterface;

final class BillingSubscription extends Message
{
    /**
     * @var DateTime
     */
    protected $subscriptionEndTime;

    /**
     * @return DateTime
     */
    public function getSubscriptionEndTime(): DateTime
    {
        return $this->subscriptionEndTime;
    }

    /**
     * @param mixed $subscriptionEndTime
     */
    public function setSubscriptionEndTime($subscriptionEndTime): void
    {
        if(!($subscriptionEndTime instanceof DateTime)) {
            $subscriptionEndTime = new DateTime($subscriptionEndTime, new DateTimeZone(DispatcherInterface::TIMEZONE));
        }

        $this->subscriptionEndTime = $subscriptionEndTime;
    }
}