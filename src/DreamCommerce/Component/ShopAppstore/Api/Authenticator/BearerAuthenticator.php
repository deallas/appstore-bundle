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

use DateTime;
use DateTimeZone;
use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Http\ClientInterface as HttpClientInterface;
use DreamCommerce\Component\ShopAppstore\Billing\DispatcherInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

abstract class BearerAuthenticator implements AuthenticatorInterface
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var ObjectManager|null
     */
    protected $tokenObjectManager;

    /**
     * @param ObjectManager $tokenObjectManager
     * @param HttpClientInterface|null $httpClient
     */
    public function __construct(ObjectManager $tokenObjectManager = null, HttpClientInterface $httpClient = null)
    {
        if($this->httpClient !== null) {
            $this->httpClient = $httpClient;
        } elseif(class_exists('\\GuzzleHttp\\Client')) {
            $this->httpClient = new AwaitGuzzleClient(new \GuzzleHttp\Client());
        }

        $this->tokenObjectManager = $tokenObjectManager;
    }

    /**
     * @param RequestInterface $request
     * @param OAuthShopInterface $shop
     */
    protected function handleRequest(RequestInterface $request, OAuthShopInterface $shop): void
    {
        try {
            $response = $this->httpClient->send($request);
        } catch(Throwable $exception) {
            // TODO
        }

        $stream = $response->getBody();
        $stream->rewind();

        $body = $stream->getContents();

        if(!$body || !is_array($body)) {
            // TODO
        } elseif(isset($body['data']['error'])) {
            // TODO
        }

        $token = $shop->getToken();
        $token->setAccessToken($body['data']['access_token']);

        $refreshToken = null;
        if(isset($body['data']['refresh_token'])) {
            $refreshToken = $body['data']['refresh_token'];
        }
        $token->setRefreshToken($refreshToken);

        $expiresAt = null;
        if(isset($body['data']['expires_in'])) {
            $expiresAt = new DateTime(
                $body['data']['expires_in'] . ' seconds',
                new DateTimeZone(DispatcherInterface::TIMEZONE)
            );
        }
        $token->setExpiresAt($expiresAt);

        if($this->tokenObjectManager !== null) {
            $this->tokenObjectManager->persist($token);
            $this->tokenObjectManager->flush();
        }
    }
}