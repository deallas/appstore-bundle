<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore;

final class ShopBillingTransitions
{
    public const GRAPH = 'dream_commerce_appstore_shop_billing';

    public const TRANSITION_PAY     = 'pay';
    public const TRANSITION_CANCEL  = 'cancel';
    public const TRANSITION_REFUND  = 'refund';

    private function __construct()
    {
    }
}