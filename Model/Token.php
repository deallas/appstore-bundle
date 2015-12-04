<?php

namespace DreamCommerce\ShopAppstoreBundle\Model;

/**
 * Class Token
 *
 * OAuth tokens instance
 *
 * @package DreamCommerce\ShopAppstoreBundle\Model
 */
abstract class Token extends ShopDependent implements TokenInterface
{
    /**
     * expiration date
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * access token
     * @var string
     */
    protected $accessToken;

    /**
     * refresh token
     * @var string
     */
    protected $refreshToken;

    /**
     * @inheritdoc
     */
    public function setExpiresAt(\DateTime $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @inheritdoc
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
}
