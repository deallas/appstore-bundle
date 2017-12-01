<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use DreamCommerce\Component\Common\Factory\DateTimeFactoryInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Subscription implements SubscriptionInterface
{
    use ShopDependTrait;
    use TimestampableTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $expiresAt;

    /**
     * @param DateTimeFactoryInterface|null $dateTimeFactory
     */
    public function __construct(?DateTimeFactoryInterface $dateTimeFactory)
    {
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
    public function setExpiresAt(?DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }
}
