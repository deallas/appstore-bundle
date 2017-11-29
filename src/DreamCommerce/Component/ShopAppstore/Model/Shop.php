<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DreamCommerce\Component\Common\Factory\DateTimeFactory;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Shop implements ShopInterface
{
    use TimestampableTrait;
    use ApplicationDependTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $state = ShopInterface::STATE_UNPAID;

    /**
     * @var integer|null
     */
    protected $version;

    /**
     * @var bool
     */
    protected $installed = false;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var Collection|SubscriptionInterface[]
     */
    protected $subscriptions;

    /**
     * @param DateTimeFactory|null $dateTimeFactory
     */
    public function __construct(?DateTimeFactory $dateTimeFactory)
    {
        $this->subscriptions = new ArrayCollection();
        if($dateTimeFactory === null) {
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
    public function isInstalled(): bool
    {
        return $this->installed;
    }

    /**
     * {@inheritdoc}
     */
    public function setInstalled(bool $installed): void
    {
        $this->installed = $installed;
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
        if(!$this->hasSubscription($subscription)) {
            $subscription->setShop($this);
            $this->subscriptions->add($subscription);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscription(SubscriptionInterface $subscription): void
    {
        if($this->hasSubscription($subscription)) {
            $subscription->setShop(null);
            $this->subscriptions->removeElement($subscription);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?integer
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion(?integer $version): void
    {
        $this->version = $version;
    }
}


