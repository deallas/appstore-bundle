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
use DreamCommerce\Component\ShopAppstore\Api\Exception\CommunicationException;
use DreamCommerce\Component\ShopAppstore\Api\Exception\LimitExceededException;
use DreamCommerce\Component\ShopAppstore\Api\Http\AwaitShopClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ItemFactory;
use DreamCommerce\Component\ShopAppstore\Factory\ItemFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ItemListFactory;
use DreamCommerce\Component\ShopAppstore\Factory\ItemListFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ItemPartListFactory;
use DreamCommerce\Component\ShopAppstore\Factory\ItemPartListFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Model\BasicAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemListInterface;
use DreamCommerce\Component\ShopAppstore\Model\ItemPartList;
use DreamCommerce\Component\ShopAppstore\Model\ItemPartListInterface;
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
     * @var ItemFactoryInterface|null
     */
    private $itemFactory;

    /**
     * @var ItemPartListFactoryInterface|null
     */
    private $itemListFactory;

    /**
     * @var ItemPartListInterface|null
     */
    private $itemPartListFactory;

    /**
     * @var ItemFactoryInterface|null
     */
    private static $globalItemFactory;

    /**
     * @var ItemListFactoryInterface|null
     */
    private static $globalItemListFactory;

    /**
     * @var ItemPartListFactoryInterface|null
     */
    private static $globalItemPartListFactory;

    /**
     * @var ShopClientInterface
     */
    private static $globalShopClient;

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
     * @param ItemFactoryInterface|null $itemFactory
     * @param ItemPartListFactoryInterface|null $itemPartListFactory
     * @param ItemListFactoryInterface|null $itemListFactory
     */
    public function __construct(ShopClientInterface $shopClient = null,
                                AuthenticatorInterface $authenticator = null,
                                ItemFactoryInterface $itemFactory = null,
                                ItemPartListFactoryInterface $itemPartListFactory = null,
                                ItemListFactoryInterface $itemListFactory = null
    ) {
        $this->shopClient = $shopClient;
        $this->authenticator = $authenticator;
        $this->itemFactory = $itemFactory;
        $this->itemPartListFactory = $itemPartListFactory;
        $this->itemListFactory = $itemListFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function find(ShopInterface $shop, int $id): ItemInterface
    {
        list($request, $response) = $this->perform($shop, 'GET', $id);

        return $this->getItemFactory()->createByApiRequest($shop, $this, $request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(ShopInterface $shop, Criteria $criteria): ItemListInterface
    {
        /** @var ItemListInterface $itemList */
        $itemList = $this->getItemListFactory()->createNew();
        $this->fetchAll($shop, $criteria, function(ItemPartList $itemPartList) use($itemList) {
            $itemList->addPart($itemPartList);
        });

        return $itemList;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPartial(ShopInterface $shop, Criteria $criteria): ItemPartListInterface
    {
        list($request, $response) = $this->perform($shop,'GET',null, null, $criteria);

        return $this->getItemPartListFactory()->createByApiRequest($shop, $this, $request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(ShopInterface $shop): ItemListInterface
    {
        return $this->findBy($shop, Criteria::create());
    }

    /**
     * {@inheritdoc}
     */
    public function walk(ShopInterface $shop, callable $callback, Criteria $criteria = null): void
    {
        if($criteria === null) {
            $criteria = Criteria::create();
        }

        $this->fetchAll($shop, $criteria, function(ItemPartList $itemPartList) use($callback) {
            foreach($itemPartList as $item) {
                call_user_func($callback, $item);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function reconnect(ItemInterface $item): void
    {
        $actualItem = $this->find($item->getShop(), $item->getExternalId());
        $item->setData($actualItem->getData()); // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function insert(ShopInterface $shop, array $data): int
    {
        list(, $response) = $this->perform($shop, 'POST', null, $data);

        return (int) $response->getBody()->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function update(ShopInterface $shop, int $id, array $data): void
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
     * {@inheritdoc}
     */
    public function insertItem(ItemInterface $item): void
    {
        if($item->hasExternalId()) {
            // TODO throw exception
        }

        $id = $this->insert($item->getShop(), $item->getData());
        $item->setExternalId($id);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem(ItemInterface $item): void
    {
        if(!$item->hasExternalId()) {
            // TODO throw exception
        }

        $this->update($item->getShop(), $item->getExternalId(), $item->getDiffData());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem(ItemInterface $item): void
    {
        if(!$item->hasExternalId()) {
            // TODO throw exception
        }

        $this->delete($item->getShop(), $item->getExternalId());
    }

    /**
     * @param ShopInterface $shop
     * @param string $method
     * @param int|null $id
     * @param array|null $data
     * @param Criteria|null $criteria
     * @return array
     * @throws CommunicationException
     */
    private function perform(ShopInterface $shop, string $method, int $id = null, array $data = null, Criteria $criteria = null): array
    {
        if(!$shop->isAuthenticated()) {
            $authenticator = $this->getAuthByShop($shop);
            $authenticator->authenticate($shop);
        }

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
                throw CommunicationException::forInvalidRequestBody($data);
            }
        }

        $request = $shopClient->getHttpClient()->createRequest(
            $method,
            $uri,
            [
                'Authorization' => 'Bearer ' . $shop->getToken()->getAccessToken(),
                'Content-Type' => 'application/json'
            ],
            $body
        );
        if($criteria !== null) {
            $criteria->fillRequest($request);
        }

        return [ $request, $shopClient->send($request) ];
    }

    /**
     * @param ShopInterface $shop
     * @param Criteria $criteria
     * @param callable $callback
     */
    private function fetchAll(ShopInterface $shop, Criteria $criteria, callable $callback)
    {
        do {
            try {
                $itemPartList = $this->findByPartial($shop, $criteria);
            } catch(LimitExceededException $exception) {
                // TODO throw
            }
            call_user_func($callback, $itemPartList);
            $criteria->nextPage();
        } while($criteria->getPage() <= $itemPartList->getTotalPages());
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
            self::$globalAuthInstances[$authClass] = new $authClass($this->getShopClient());
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
            self::$globalShopClient = new AwaitShopClient();
        }

        return self::$globalShopClient;
    }

    /**
     * @return ItemFactoryInterface
     */
    private function getItemFactory(): ItemFactoryInterface
    {
        if($this->itemFactory !== null) {
            return $this->itemFactory;
        }

        if(self::$globalItemFactory === null) {
            self::$globalItemFactory = new ItemFactory();
        }

        return self::$globalItemFactory;
    }

    /**
     * @return ItemListFactoryInterface
     */
    private function getItemListFactory(): ItemListFactoryInterface
    {
        if($this->itemListFactory !== null) {
            return $this->itemListFactory;
        }

        if(self::$globalItemListFactory === null) {
            self::$globalItemListFactory = new ItemListFactory();
        }

        return self::$globalItemListFactory;
    }

    /**
     * @return ItemPartListFactoryInterface
     */
    private function getItemPartListFactory(): ItemPartListFactoryInterface
    {
        if($this->itemPartListFactory !== null) {
            return $this->itemPartListFactory;
        }

        if(self::$globalItemPartListFactory === null) {
            self::$globalItemPartListFactory = new ItemPartListFactory($this->getItemFactory());
        }

        return self::$globalItemPartListFactory;
    }
}