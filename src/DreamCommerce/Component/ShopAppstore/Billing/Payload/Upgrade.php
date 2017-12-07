<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Payload;

final class Upgrade extends Message
{
    /**
     * @var integer
     */
    protected $applicationVersion;

    /**
     * @return int
     */
    public function getApplicationVersion(): int
    {
        return $this->applicationVersion;
    }

    /**
     * @param mixed $applicationVersion
     */
    protected function setApplicationVersion($applicationVersion): void
    {
        $this->applicationVersion = (int) $applicationVersion;
    }
}