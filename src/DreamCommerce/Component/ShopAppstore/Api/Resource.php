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
use DreamCommerce\Component\ShopAppstore\Api\Resource\Exception as ResourceException;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

abstract class Resource implements ResourceInterface
{
    /**
     * @var AuthenticatorInterface
     */
    private $authenticator;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @param AuthenticatorInterface|null $authenticator
     * @param HttpClientInterface|null $httpClient
     */
    public function __construct(AuthenticatorInterface $authenticator = null, HttpClientInterface $httpClient = null)
    {
        $this->authenticator = $authenticator;

        if($this->httpClient !== null) {
            $this->httpClient = $httpClient;
        } elseif(class_exists('\\GuzzleHttp\\Client')) {
            $this->httpClient = new AwaitGuzzleClient(new \GuzzleHttp\Client());
        }
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

    private function parseArgs(array $args): array
    {
        if(count($args) !== 1) {
            return $args;
        }

        if(is_array($args[0])) {
            return $args[0];
        }

    }

    private function handleRequest(ShopInterface $shop, RequestInterface $request)
    {
        $this->authenticator->authenticate($shop);

        try {
            $response = $this->httpClient->send($request);
        } catch(Throwable $exception) {

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

    protected function handleInvalidHttpCode(int $code)
    {
        switch ($code) {
            case 400:
                throw new ResourceException\ValidationException($httpException->getResponse(), 0, $httpException);
            case 404:
                throw new ResourceException\NotFoundException($httpException->getResponse(), 0, $httpException);
            case 405:
                throw new ResourceException\MethodUnsupportedException($httpException->getResponse(), 0, $httpException);
            case 409:
                throw new ResourceException\ObjectLockedException($httpException->getResponse(), 0, $httpException);
            case 401:
                throw new ResourceException\PermissionsException($httpException->getResponse(), 0, $httpException);
        }
    }
}