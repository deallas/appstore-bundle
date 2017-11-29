<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use Doctrine\Common\Collections\Collection;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ShopInterface extends ResourceInterface, TimestampableInterface, ApplicationDependInterface
{
    public const STATE_UNPAID       = 'unpaid';
    public const STATE_PAID         = 'paid';
    public const STATE_REFUNDED     = 'refunded';
    public const STATE_CANCELLED    = 'cancelled';

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return bool
     */
    public function isInstalled(): bool;

    /**
     * @param bool $installed
     */
    public function setInstalled(bool $installed): void;

    /**
     * @return null|string
     */
    public function getState(): ?string;

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void;

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