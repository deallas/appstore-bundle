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

use ArrayObject;
use DreamCommerce\Component\Common\Http\ClientInterface as HttpClientInterface;
use DreamCommerce\Component\Common\Http\GuzzleClient as GuzzlePsrClient;
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\AuthenticatorInterface;
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\BasicAuthAuthenticator;
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\OAuthAuthenticator;
use DreamCommerce\Component\ShopAppstore\Api\Http\AwaitShopClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Api\Hydrator\ItemHydrator;
use DreamCommerce\Component\ShopAppstore\Api\Hydrator\HydratorInterface;
use DreamCommerce\Component\ShopAppstore\Model\BasicAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemListInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use RuntimeException;

abstract class Resource implements ResourceInterface
{
    /**
     * @var ShopClientInterface|null
     */
    private $shopClient;

    /**
     * @var AuthenticatorInterface|null
     */
    private $authenticator;

    /**
     * @var HydratorInterface|null
     */
    private $hydrator;

    /**
     * @var HydratorInterface|null
     */
    private static $globalHydrator;

    /**
     * @var ShopClientInterface
     */
    private static $globalShopClient;

    /**
     * @var HttpClientInterface
     */
    private static $globalHttpClient;

    /**
     * @var array
     */
    private static $globalAuthMap = [
        BasicAuthShopInterface::class   => BasicAuthAuthenticator::class,
        OAuthShopInterface::class       => OAuthAuthenticator::class
    ];

    /**
     * @var AuthenticatorInterface[]
     */
    private static $globalAuthInstances = [];

    /**
     * @param ShopClientInterface|null $shopClient
     * @param AuthenticatorInterface|null $authenticator
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(ShopClientInterface $shopClient = null,
                                AuthenticatorInterface $authenticator = null,
                                HydratorInterface $hydrator = null
    ) {
        $this->shopClient = $shopClient;
        $this->authenticator = $authenticator;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ShopInterface $shop, ...$args): ArrayObject
    {
        list($id, $criteria) = $this->parseArgs($args);
        list($request, $response) = $this->perform($shop, 'GET', $id, null, $criteria);

        return $this->getHydrator()->hydrate($this, $request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function head(ShopInterface $shop, ...$args): ItemListInterface
    {
        list($id, $criteria) = $this->parseArgs($args);
        list($request, $response) = $this->perform($shop, 'HEAD', $id, null, $criteria);

        return $this->getHydrator()->hydrate($this, $request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function post(ShopInterface $shop, array $data): int
    {
        list($request, $response) = $this->perform($shop, 'POST', null, $data);

        return (int) $response->getBody()->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function put(ShopInterface $shop, int $id, array $data): void
    {
        $this->perform($shop, 'PUT', $id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ShopInterface $shop, int $id): void
    {
        $this->perform($shop, 'DELETE', $id);
    }

    /**
     * @param array $args
     * @return array
     */
    private function parseArgs(array $args): array
    {
        if(count($args) === 1) {
            if(is_numeric($args[0]) && (int)$args[0] == $args[0]) {
                return [$args[0], null];
            } elseif($args[0] instanceof Criteria) {
                return [ null, $args[0] ];
            } else {
                // TODO throw exception
            }
        }
    }

    /**
     * @param ShopInterface $shop
     * @param string $method
     * @param int|null $id
     * @param array|null $data
     * @param Criteria|null $criteria
     * @return array
     */
    private function perform(ShopInterface $shop, string $method, int $id = null, array $data = null, Criteria $criteria = null): array
    {
        if(!$shop->isAuthenticated()) {
            $authenticator = $this->getAuthByShop($shop);
            $authenticator->authenticate($shop);
        }

        $httpClient = $this->getHttpClient();
        $shopClient = $this->getShopClient();

        $uri = $shop->getUri();
        $uri = $uri->withPath($uri->getPath() . '/webapi/rest/' . $this->getName());

        if($id !== null) {
            $uri = $uri->withPath($uri->getPath() . '/' . $id);
        }

        $body = null;
        if($data !== null && in_array($method, [ 'POST', 'PUT' ])) {
            $body = @json_encode($data);
            if ($body === false) {
                // TODO throw exception
            }
        }

        $request = $httpClient->createRequest(
            $method,
            $uri,
            [
                'Authorization' => 'Bearer ' . $shop->getToken()->getAccessToken(),
                'Content-Type' => 'application/json',
                'User-Agent' => 'DreamCommerce ShopAppStore Agent', // TODO
                'Accept-Language' => 'en_US;q=0.8' // TODO
            ],
            $body
        );
        $criteria->fillRequest($request);

        return [ $request, $shopClient->send($request) ];
    }

    /**
     * @param ShopInterface $shop
     * @return AuthenticatorInterface
     */
    private function getAuthByShop(ShopInterface $shop): AuthenticatorInterface
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
    private function getAuthInstance(string $authClass): AuthenticatorInterface
    {
        if(!isset(self::$globalAuthInstances[$authClass])) {
            self::$globalAuthInstances[$authClass] = new $authClass($this->getHttpClient());
        }

        return self::$globalAuthInstances[$authClass];
    }

    /**
     * @return ShopClientInterface
     */
    private function getShopClient(): ShopClientInterface
    {
        if($this->shopClient !== null) {
            return $this->shopClient;
        }

        if(self::$globalShopClient === null) {
            self::$globalShopClient = new AwaitShopClient($this->getHttpClient());
        }

        return self::$globalShopClient;
    }

    /**
     * @return HttpClientInterface
     */
    private function getHttpClient(): HttpClientInterface
    {
        if(self::$globalHttpClient === null) {
            if (class_exists('\\GuzzleHttp\\Client')) {
                self::$globalHttpClient = new GuzzlePsrClient(new \GuzzleHttp\Client());
            } else {
                throw new RuntimeException('Unable initialize HTTP client');
            }
        }

        return self::$globalHttpClient;
    }

    /**
     * @return HydratorInterface
     */
    private function getHydrator(): HydratorInterface
    {
        if($this->hydrator !== null) {
            return $this->hydrator;
        }

        if(self::$globalHydrator === null) {
            self::$globalHydrator = new ItemHydrator();
        }

        return self::$globalHydrator;
    }
}