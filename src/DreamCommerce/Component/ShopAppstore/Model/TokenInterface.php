<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use Sylius\Component\Resource\Model\ResourceInterface;

interface TokenInterface extends ResourceInterface, ShopDependInterface
{
    /**
     * @param DateTime $expiresAt
     */
    public function setExpiresAt(?DateTime $expiresAt): void;

    /**
     * @return DateTime|null
     */
    public function getExpiresAt(): ?DateTime;

    /**
     * @param string $accessToken
     */
    public function setAccessToken(?string $accessToken): void;

    /**
     * @return null|string
     */
    public function getAccessToken(): ?string;

    /**
     * @param string|null $refreshToken
     */
    public function setRefreshToken(?string $refreshToken): void;

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string;
}