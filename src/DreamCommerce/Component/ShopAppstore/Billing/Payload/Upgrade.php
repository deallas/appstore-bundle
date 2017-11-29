<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

final class Upgrade extends Message
{
    /**
     * @var integer
     */
    private $applicationVersion;

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return Message::ACTION_UPGRADE;
    }

    /**
     * @return int
     */
    public function getApplicationVersion(): int
    {
        return $this->applicationVersion;
    }
}