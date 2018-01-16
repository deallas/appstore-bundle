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
use DreamCommerce\Component\ShopAppstore\Api\Authenticator\OAuthAuthenticator;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use PHPUnit\Framework\MockObject\MockObject;

class OAuthAuthenticatorTest extends BearerAuthenticatorTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->authenticator = new OAuthAuthenticator($this->shopClient, $this->dateTimeFactory);
    }

    protected function prepareValidResponse(ShopInterface $shop, string $accessToken, ?string $refreshToken, int $expiresIn, DateTime $dateTime)
    {
        /** @var ShopInterface|MockObject $shop */
        $shop->expects($this->once())
            ->method('setAuthCode')
            ->willReturnCallback(function($fAuthCode) {
                $this->assertNull($fAuthCode);
            });

        parent::prepareValidResponse($shop, $accessToken, $refreshToken, $expiresIn, $dateTime);
    }

    protected function getShop(): ShopInterface
    {
        $application = $this->getMockBuilder(ApplicationInterface::class)->getMock();
        $application->expects($this->once())
            ->method('getAppId')
            ->willReturn('5555');

        $application->expects($this->once())
            ->method('getAppSecret')
            ->willReturn('4444');

        /** @var ShopInterface|MockObject $shop */
        $shop = $this->getMockBuilder(OAuthShopInterface::class)->getMock();
        $shop->expects($this->once())
            ->method('getAuthCode')
            ->willReturn('test');

        $shop->expects($this->once())
            ->method('getApplication')
            ->willReturn($application);

        $shop->expects($this->once())
            ->method('getUri')
            ->willReturn($this->getUri());

        return $shop;
    }
}