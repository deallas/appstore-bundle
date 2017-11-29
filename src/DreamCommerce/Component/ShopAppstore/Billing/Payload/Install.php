<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

final class Install extends Message
{
    /**
     * @var integer
     */
    private $applicationVersion;

    /**
     * @var string
     */
    private $authCode;

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return Message::ACTION_INSTALL;
    }

    /**
     * @return int
     */
    public function getApplicationVersion(): int
    {
        return $this->applicationVersion;
    }

    /**
     * @return string
     */
    public function getAuthCode(): string
    {
        return $this->authCode;
    }
}