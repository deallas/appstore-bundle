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

namespace DreamCommerce\Component\ShopAppstore\Api\Authenticator;

use DateInterval;
use DateTimeZone;
use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Factory\DateTimeFactory;
use DreamCommerce\Component\Common\Factory\DateTimeFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Api\Exception;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Info;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\RequestInterface;

abstract class BearerAuthenticator implements AuthenticatorInterface
{
    /**
     * @var DateTimeFactoryInterface
     */
    private $dateTimeFactory;

    /**
     * @var ShopClientInterface
     */
    protected $shopClient;

    /**
     * @var ObjectManager|null
     */
    protected $tokenObjectManager;

    /**
     * @param ShopClientInterface $shopClient
     * @param DateTimeFactoryInterface|null $dateTimeFactory
     * @param ObjectManager|null $tokenObjectManager
     */
    public function __construct(ShopClientInterface $shopClient, DateTimeFactoryInterface $dateTimeFactory = null, ObjectManager $tokenObjectManager = null)
    {
        $this->shopClient = $shopClient;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->tokenObjectManager = $tokenObjectManager;
    }

    /**
     * @param RequestInterface $request
     * @param ShopInterface $shop
     * @throws Exception\AuthenticationFailedException
     * @throws Exception\CommunicationException
     */
    protected function handleRequest(RequestInterface $request, ShopInterface $shop): void
    {
        $exception = null;

        try {
            $response = $this->shopClient->send($request);
        } catch(Exception\PermissionsException $exception) {
            $response = $exception->getHttpResponse();
        }

        $stream = $response->getBody();
        $stream->rewind();

        $body = $stream->getContents();
        if(strlen($body) === 0) {
            throw Exception\CommunicationException::forEmptyResponseBody($request, $response, $exception);
        }
        $body = @json_decode($body, true);

        if(!$body || !is_array($body)) {
            throw Exception\CommunicationException::forUnsupportedResponseBody($request, $response, $exception);
        } elseif(isset($body['error'])) {
            throw Exception\AuthenticationFailedException::forInvalidResponseBody($body, $request, $response, $exception);
        }

        $token = $shop->getToken();
        $token->setAccessToken($body['access_token']);

        $refreshToken = null;
        if(isset($body['refresh_token'])) {
            $refreshToken = $body['refresh_token'];
        }
        $token->setRefreshToken($refreshToken);

        $expiresAt = null;
        if(isset($body['expires_in'])) {
            $dateTimeFactory = $this->getDateTimeFactory();
            $expiresAt = $dateTimeFactory->createNewWithTimezone(new DateTimeZone(Info::TIMEZONE));
            $expiresAt->add(DateInterval::createFromDateString($body['expires_in'] . ' seconds'));
        }
        $token->setExpiresAt($expiresAt);

        if($this->tokenObjectManager !== null) {
            $this->tokenObjectManager->persist($token);
            $this->tokenObjectManager->flush();
        }
    }

    /**
     * @return DateTimeFactoryInterface
     */
    private function getDateTimeFactory(): DateTimeFactoryInterface
    {
        if($this->dateTimeFactory === null) {
            $this->dateTimeFactory = new DateTimeFactory();
        }

        return $this->dateTimeFactory;
    }
}