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
use DreamCommerce\Component\Common\Http\GuzzleClient as GuzzlePsrClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\AwaitShopClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Model\BasicAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use GuzzleHttp\Client as GuzzleHttpClient;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

abstract class Resource implements ResourceInterface
{
    /**
     * @var ShopClientInterface
     */
    private $shopClient;

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
     */
    public function __construct(ShopClientInterface $shopClient = null)
    {
        $this->shopClient = $shopClient;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ShopInterface $shop, ...$args): ArrayObject
    {
        $args = $this->parseArgs($args);
    }

    /**
     * {@inheritdoc}
     */
    public function head(ShopInterface $shop, ...$args): ArrayObject
    {
        $args = $this->parseArgs($args);
    }

    /**
     * {@inheritdoc}
     */
    public function post(ShopInterface $shop, array $data): int
    {
        // TODO: Implement post() method.
    }

    /**
     * {@inheritdoc}
     */
    public function put(ShopInterface $shop, int $id, array $data): void
    {
        // TODO: Implement put() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ShopInterface $shop, int $id): void
    {
        $this->authenticator->authenticate($shop);

        try {
            $this->httpClient->request($this, 'delete', $args);
        } catch(Throwable $ex) {
            $this->dispatchException($ex);
        }
    }

    /**
     * @param string $shopClass
     * @param string $authClass
     */
    public static function setAuthMap(string $shopClass, string $authClass): void
    {
        self::$authMap[$shopClass] = $authClass;
    }

    /**
     * @param array $args
     * @return array
     */
    private function parseArgs(array $args): array
    {
        if(count($args) !== 1) {
            return $args;
        }

        if(is_array($args[0])) {
            return $args[0];
        }

        // TODO
    }

    private function perform(ShopInterface $shop, string $method, array $data)
    {

    }

    /**
     * @param ShopInterface $shop
     * @return AuthenticatorInterface
     */
    private function getAuthByShop(ShopInterface $shop): AuthenticatorInterface
    {
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
            self::$authInstances[$authClass] = new $authClass;
        }

        return self::$authInstances[$authClass];
    }

    /**
     * @return ShopClientInterface
     */
    private function getShopClient(): ShopClientInterface
    {
        if($this->shopClient === null) {
            $this->shopClient = new AwaitShopClient(
                new GuzzlePsrClient(
                    new GuzzleHttpClient()
                )
            );
        }

        return $this->shopClient;
    }

    /**
     * @param ResponseInterface $response
     * @param bool $isCollection should transform response as a collection?
     * @throws ResourceException
     * @return mixed
     */
    protected function transformResponse(ResponseInterface $response, bool $isCollection)
    {
        $code = null;
        if (isset($response['headers']['Code'])) {
            $code = $response['headers']['Code'];
        }

        // everything is okay when 200-299 status code
        if ($code >= 200 && $code < 300) {
            // for example, last insert ID
            if ($isCollection) {
                if (isset($response['data']['list'])) {
                    $objectList = new ResourceList($response['data']['list']);
                } else {
                    $objectList = new ResourceList();
                }

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
}