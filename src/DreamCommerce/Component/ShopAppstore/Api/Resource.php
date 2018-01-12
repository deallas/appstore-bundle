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
use DreamCommerce\Component\ShopAppstore\Api\Http\AwaitShopClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Api\Resource\IdentifierAwareInterface;
use DreamCommerce\Component\ShopAppstore\Model\BasicAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\ResponseInterface;
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
     * @var HttpClientInterface
     */
    private static $httpClient;

    /**
     * @var array
     */
    private static $authMap = [
        BasicAuthShopInterface::class   => BasicAuthAuthenticator::class,
        OAuthShopInterface::class       => OAuthAuthenticator::class
    ];

    /**
     * @var AuthenticatorInterface[]
     */
    private static $authInstances = [];

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
     * {@inheritdoc}
     */
    public function get(ShopInterface $shop, ...$args): ArrayObject
    {
        list($id, $criteria) = $this->parseArgs($args);
        $response = $this->perform($shop, 'GET', $id, null, $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function head(ShopInterface $shop, ...$args): ArrayObject
    {
        list($id, $criteria) = $this->parseArgs($args);
    }

    /**
     * {@inheritdoc}
     */
    public function post(ShopInterface $shop, array $data): int
    {
        $response = $this->perform($shop, 'POST', null, $data);
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
     * @param ResponseInterface $response
     * @param bool $isCollection should transform response as a collection?
     * @throws ResourceException
     * @return mixed
     */
    protected function transformResponse(ResponseInterface $response, bool $isCollection)
    {
        $code = $response->getStatusCode();
        $data = @json_decode($response->getBody());
        if($data === false) {
            // TODO throw exception
        }

        // everything is okay when 200-299 status code
        if ($code >= 200 && $code < 300) {
            if ($this instanceof IdentifierAwareInterface) {
                if (isset($data['list'])) {
                    $objectList = new ResourceList($data['list']);
                } else {
                    $objectList = new ResourceList();
                }

                $headers = $response->getHeaders();

                // add meta properties (eg. count, page, etc) as a ArrayObject properties
                if (isset($response['data']['page'])) {
                    $objectList->setPage($response['data']['page']);
                } elseif (isset($response['headers']['X-Shop-Result-Page'])) {
                    $objectList->setPage($response['headers']['X-Shop-Result-Page']);
                }

                if (isset($response['data']['count'])) {
                    $objectList->setCount($response['data']['count']);
                } elseif (isset($response['headers']['X-Shop-Result-Count'])) {
                    $objectList->setCount($response['headers']['X-Shop-Result-Count']);
                }

                if (isset($response['data']['pages'])) {
                    $objectList->setPageCount($response['data']['pages']);
                } elseif (isset($response['headers']['X-Shop-Result-Pages'])) {
                    $objectList->setPageCount($response['headers']['X-Shop-Result-Pages']);
                }

                return $objectList;
            } else {
                $result = $response['data'];
                if (!is_scalar($response['data'])) {
                    $result = new ArrayObject(ResourceList::transform($result));
                }

                return $result;
            }

        } else {
            if (isset($response['data']['error'])) {
                $msg = $response['data']['error'];
            } else {
                $msg = $response;
            }

            throw new ResourceException($msg, $code); // TODO
        }
    }

    /**
     * @param ShopInterface $shop
     * @param string $method
     * @param int|null $id
     * @param array|null $data
     * @param Criteria|null $criteria
     * @return ResponseInterface
     */
    private function perform(ShopInterface $shop, string $method, int $id = null, array $data = null, Criteria $criteria = null): ResponseInterface
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

        $shopClient->send($request);
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

        foreach(self::$authMap as $shopClass => $authClass) {
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
        if(!isset(self::$authInstances[$authClass])) {
            self::$authInstances[$authClass] = new $authClass($this->getHttpClient());
        }

        return self::$authInstances[$authClass];
    }

    /**
     * @return ShopClientInterface
     */
    private function getShopClient(): ShopClientInterface
    {
        if($this->shopClient === null) {
            if(class_exists('\\GuzzleHttp\\Client')) {
                $this->shopClient = new AwaitShopClient($this->getHttpClient());
            } else {
                throw new RuntimeException('Unable initialize shop client');
            }
        }

        return $this->shopClient;
    }

    /**
     * @return HttpClientInterface
     */
    private function getHttpClient(): HttpClientInterface
    {
        if(self::$httpClient === null) {
            if (class_exists('\\GuzzleHttp\\Client')) {
                self::$httpClient = new GuzzlePsrClient(new \GuzzleHttp\Client());
            } else {
                throw new RuntimeException('Unable initialize HTTP client');
            }
        }

        return self::$httpClient;
    }
}