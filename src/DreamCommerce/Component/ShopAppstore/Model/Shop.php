<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DreamCommerce\Component\Common\Factory\DateTimeFactoryInterface;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Shop implements ShopInterface
{
    use TimestampableTrait;
    use ApplicationDependTrait;

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $state;

    /**
     * @var string|null
     */
    private $billingState;

    /**
     * @var string|null
     */
    private $subscriptionState;

    /**
     * @var int|null
     */
    private $version;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var Collection|SubscriptionInterface[]
     */
    private $subscriptions;

    /**
     * @param DateTimeFactoryInterface|null $dateTimeFactory
     */
    public function __construct(?DateTimeFactoryInterface $dateTimeFactory)
    {
        $this->subscriptions = new ArrayCollection();
        if ($dateTimeFactory === null) {
            $this->createdAt = new DateTime();
        } else {
            $this->createdAt = $dateTimeFactory->createNew();
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingState(?string $billingState): void
    {
        $this->billingState = $billingState;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingState(): ?string
    {
        return $this->billingState;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscriptionState(?string $subscriptionState): void
    {
        $this->subscriptionState = $subscriptionState;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriptionState(): ?string
    {
        return $this->subscriptionState;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setUri(?UriInterface $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): ?UriInterface
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): ?TokenInterface
    {
        return $this->token;
    }

    public function setToken(?TokenInterface $token): void
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSubscription(SubscriptionInterface $subscription): bool
    {
        return $this->subscriptions->contains($subscription);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscription(SubscriptionInterface $subscription): void
    {
        if (!$this->hasSubscription($subscription)) {
            $subscription->setShop($this);
            $this->subscriptions->add($subscription);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscription(SubscriptionInterface $subscription): void
    {
        if ($this->hasSubscription($subscription)) {
            $subscription->setShop(null);
            $this->subscriptions->removeElement($subscription);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }
}
