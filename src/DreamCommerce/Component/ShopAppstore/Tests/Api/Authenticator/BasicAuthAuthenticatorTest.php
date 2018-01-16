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

use DreamCommerce\Component\ShopAppstore\Api\Authenticator\BasicAuthAuthenticator;
use DreamCommerce\Component\ShopAppstore\Model\BasicAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use PHPUnit\Framework\MockObject\MockObject;

class BasicAuthAuthenticatorTest extends BearerAuthenticatorTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->authenticator = new BasicAuthAuthenticator($this->shopClient, $this->dateTimeFactory);
    }

    protected function getShop(): ShopInterface
    {
        /** @var ShopInterface|MockObject $shop */
        $shop = $this->getMockBuilder(BasicAuthShopInterface::class)->getMock();
        $shop->expects($this->once())
            ->method('getUsername')
            ->willReturn('test');

        $shop->expects($this->once())
            ->method('getPassword')
            ->willReturn('pass');

        $shop->expects($this->once())
            ->method('getUri')
            ->willReturn($this->getUri());

        return $shop;
    }
}