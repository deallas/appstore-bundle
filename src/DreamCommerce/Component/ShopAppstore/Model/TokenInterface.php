<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

use DateTime;
use Sylius\Component\Resource\Model\ResourceInterface;

interface TokenInterface extends ResourceInterface
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
     * @param string $refreshToken
     * @return mixed
     */
    public function setRefreshToken($refreshToken);

    /**
     * get refresh token
     * @return string
     */
    public function getRefreshToken();

    /**
     * set shop
     * @param ShopInterface $shop
     * @return mixed
     */
    public function setShop(ShopInterface $shop);

    /**
     * get shop
     * @return ShopInterface
     */
    public function getShop();
}