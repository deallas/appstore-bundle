<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use Doctrine\Common\Collections\Collection;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ShopInterface extends ResourceInterface, TimestampableInterface, ApplicationDependInterface
{
    public const STATE_NEW                  = 'new';
    public const STATE_UNINSTALLED          = 'uninstalled';
    public const STATE_PREFETCH_TOKENS      = 'prefetch_tokens';
    public const STATE_INSTALLED            = 'installed';

    public const STATE_BILLING_UNPAID       = 'unpaid';
    public const STATE_BILLING_PAID         = 'paid';
    public const STATE_BILLING_REFUNDED     = 'refunded';
    public const STATE_BILLING_CANCELLED    = 'cancelled';

    public const STATE_SUBSCRIPTION_UNPAID  = 'unpaid';
    public const STATE_SUBSCRIPTION_PAID    = 'paid';
    public const STATE_SUBSCRIPTION_EXPIRED = 'expired';

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return null|string
     */
    public function getState(): ?string;

    /**
     * @param null|string $state
     */
    public function setState(?string $state): void;

    /**
     * @return null|string
     */
    public function getBillingState(): ?string;

    /**
     * @param string|null $billingState
     */
    public function setBillingState(?string $billingState): void;

    /**
     * @return null|string
     */
    public function getSubscriptionState(): ?string;

    /**
     * @param null|string $subscriptionState
     */
    public function setSubscriptionState(?string $subscriptionState): void;

    /**
     * @param UriInterface $uri
     */
    public function setUri(?UriInterface $uri): void;

    /**
     * @return UriInterface|null
     */
    public function getUri(): ?UriInterface;

    /**
     * @return TokenInterface|null
     */
    public function getToken(): ?TokenInterface;

    /**
     * @param TokenInterface $token
     */
    public function setToken(?TokenInterface $token): void;

    /**
     * @return Collection|SubscriptionInterface[]
     */
    public function getSubscriptions(): Collection;

    /**
     * @param SubscriptionInterface $subscription
     * @return bool
     */
    public function hasSubscription(SubscriptionInterface $subscription): bool;

    /**
     * @param SubscriptionInterface $subscription
     */
    public function addSubscription(SubscriptionInterface $subscription): void;

    /**
     * @param SubscriptionInterface $subscription
     */
    public function removeSubscription(SubscriptionInterface $subscription): void;

    /**
     * @return integer|null
     */
    public function getVersion(): ?integer;

    /**
     * @param integer $version
     */
    public function setVersion(?integer $version): void;
}