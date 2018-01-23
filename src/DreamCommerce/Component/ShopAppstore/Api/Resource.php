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

namespace DreamCommerce\Component\ShopAppstore\Api;

use DreamCommerce\Component\ShopAppstore\Api\Authenticator\AuthenticatorInterface;
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\BasicAuthAuthenticator;
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\OAuthAuthenticator;
use DreamCommerce\Component\ShopAppstore\Api\Http\AwaitShopClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Factory\DataContainerFactory;
use DreamCommerce\Component\ShopAppstore\Factory\DataContainerFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Model\BasicAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use RuntimeException;

abstract class Resource implements ResourceInterface
{
    /**
     * @var ShopClientInterface|null
     */
    protected $shopClient;

    /**
     * @var AuthenticatorInterface|null
     */
    protected $authenticator;

    /**
     * @var DataContainerFactoryInterface
     */
    protected static $globalDataContainerFactory;

    /**
     * @var ShopClientInterface
     */
    protected static $globalShopClient;

    /**
     * @var array
     */
    protected static $globalAuthMap = [
        BasicAuthShopInterface::class   => BasicAuthAuthenticator::class,
        OAuthShopInterface::class       => OAuthAuthenticator::class
    ];

    /**
     * @var AuthenticatorInterface[]
     */
    protected static $globalAuthInstances = [];

    /**
     * @param ShopClientInterface|null $shopClient
     * @param AuthenticatorInterface|null $authenticator
     */
    public function __construct(ShopClientInterface $shopClient = null, AuthenticatorInterface $authenticator = null)
    {
        $this->shopClient = $shopClient;
        $this->authenticator = $authenticator;
    }

    /**
     * @param ShopInterface $shop
     * @return AuthenticatorInterface
     */
    protected function getAuthByShop(ShopInterface $shop): AuthenticatorInterface
    {
        if($this->authenticator !== null) {
            return $this->authenticator;
        }

        foreach(self::$globalAuthMap as $shopClass => $authClass) {
            if($shop instanceof $shopClass) {
                return $this->getAuthInstance($authClass);
            }
        }

        throw new RuntimeException('Unable find authenticator for class "' . get_class($shop) . '"');
    }

    /**
     * @param string $authClass
     * @return AuthenticatorInterface
     */
    protected function getAuthInstance(string $authClass): AuthenticatorInterface
    {
        if(!isset(self::$globalAuthInstances[$authClass])) {
            self::$globalAuthInstances[$authClass] = new $authClass($this->getShopClient());
        }

        return self::$globalAuthInstances[$authClass];
    }

    /**
     * @return ShopClientInterface
     */
    protected function getShopClient(): ShopClientInterface
    {
        if($this->shopClient !== null) {
            return $this->shopClient;
        }

        if(self::$globalShopClient === null) {
            self::$globalShopClient = new AwaitShopClient();
        }

        return self::$globalShopClient;
    }

    /**
     * @return DataContainerFactoryInterface
     */
    protected function getGlobalDataContainerFactory()
    {
        if(self::$globalDataContainerFactory === null) {
            self::$globalDataContainerFactory = new DataContainerFactory();
        }

        return self::$globalDataContainerFactory;
    }
}