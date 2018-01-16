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

namespace DreamCommerce\Component\ShopAppstore\Tests\Api\Authenticator;

use DateTime;
use DreamCommerce\Component\Common\Factory\DateTimeFactoryInterface;
use DreamCommerce\Component\Common\Http\ClientInterface as HttpClientInterface;
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\AuthenticatorInterface;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

abstract class BearerAuthenticatorTest extends TestCase
{
    /**
     * @var ShopClientInterface|MockObject
     */
    protected $shopClient;

    /**
     * @var HttpClientInterface|MockObject
     */
    protected $httpClient;

    /**
     * @var DateTimeFactoryInterface|MockObject
     */
    protected $dateTimeFactory;

    /**
     * @var AuthenticatorInterface
     */
    protected $authenticator;

    public function setUp(): void
    {
        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $this->shopClient = $this->getMockBuilder(ShopClientInterface::class)->getMock();
        $this->dateTimeFactory = $this->getMockBuilder(DateTimeFactoryInterface::class)->getMock();
    }

    public function testShouldImplements(): void
    {
        $this->assertInstanceOf(AuthenticatorInterface::class, $this->authenticator);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAuthenticateInvalidParameter(): void
    {
        /** @var ShopInterface|MockObject $shop */
        $shop = $this->getMockBuilder(ShopInterface::class)->getMock();
        $this->authenticator->authenticate($shop);
    }

    public function testAuthenticateValid(): void
    {
        $accessToken = '12345';
        $refreshToken = '23456';
        $expiresIn = 125;
        $dateTime = new DateTime('2018-01-16 20:30:45');

        $this->prepareRequest([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
            'scopes' => [
                'A', 'B', 'C'
            ]
        ]);

        $shop = $this->getShop();
        $this->prepareValidResponse($shop, $accessToken, $refreshToken, $expiresIn, $dateTime);

        $this->authenticator->authenticate($shop);
    }

    protected function prepareRequest(array $body)
    {
        $this->shopClient->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($this->httpClient);

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();

        $this->httpClient->expects($this->once())
            ->method('createRequest')
            ->willReturn($request);

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();
        $stream->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode($body));

        $stream->expects($this->once())
            ->method('rewind');

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $this->shopClient->expects($this->once())
            ->method('send')
            ->willReturn($response);
    }

    protected function prepareValidResponse(ShopInterface $shop, string $accessToken, ?string $refreshToken, int $expiresIn, DateTime $dateTime)
    {
        /** @var TokenInterface|MockObject $token */
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();

        $shop->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $token->expects($this->once())
            ->method('setAccessToken')
            ->willReturnCallback(function($fAccessToken) use($accessToken) {
                $this->assertEquals($accessToken, $fAccessToken);
            });

        $token->expects($this->once())
            ->method('setRefreshToken')
            ->willReturnCallback(function($fRefreshToken) use($refreshToken) {
                $this->assertEquals($refreshToken, $fRefreshToken);
            });

        $this->dateTimeFactory->expects($this->once())
            ->method('createNewWithTimezone')
            ->willReturn(clone $dateTime);

        $token->expects($this->once())
            ->method('setExpiresAt')
            ->willReturnCallback(function(DateTime $fDateTime) use($dateTime, $expiresIn) {
                $this->assertEquals($expiresIn, $fDateTime->getTimestamp() - $dateTime->getTimestamp());
            });
    }

    /**
     * @return UriInterface|MockObject
     */
    protected function getUri(): UriInterface
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->expects($this->once())
            ->method('withPath')
            ->willReturn($uri);

        $uri->expects($this->once())
            ->method('withQuery')
            ->willReturn($uri);

        return $uri;
    }

    /**
     * @return ShopInterface|MockObject
     */
    abstract protected function getShop(): ShopInterface;
}