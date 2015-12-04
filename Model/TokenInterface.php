<?php

namespace DreamCommerce\ShopAppstoreBundle\Model;

/**
 * Interface TokenInterface
 *
 * tokens information
 *
 * @package DreamCommerce\ShopAppstoreBundle\Model
 */
interface TokenInterface extends ShopDependentInterface
{
    /**
     * set expiration date
     * @param \DateTime $expiresAt
     * @return void
     */
    public function setExpiresAt(\DateTime $expiresAt);

    /**
     * get expiration date
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * set access token
     * @param string $accessToken
     * @return mixed
     */
    public function setAccessToken($accessToken);

    /**
     * get access token
     * @return string
     */
    public function getAccessToken();

    /**
     * set refresh token
     * @param string $refreshToken
     * @return mixed
     */
    public function setRefreshToken($refreshToken);

    /**
     * get refresh token
     * @return string
     */
    public function getRefreshToken();
}