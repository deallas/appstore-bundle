<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ShopInterface extends ResourceInterface
{
    /**
     * @param BillingInterface|null $billing
     */
    public function setBilling(?BillingInterface $billing): void;

    /**
     * @return BillingInterface|null
     */
    public function getBilling(): ?BillingInterface;

    /**
     * @param string $shopUrl
     */
    public function setShopUrl(string $shopUrl): void;

    /**
     * get shop url
     * @return string
     */
    public function getShopUrl();

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
    public function setVersion(integer $version): void;
}