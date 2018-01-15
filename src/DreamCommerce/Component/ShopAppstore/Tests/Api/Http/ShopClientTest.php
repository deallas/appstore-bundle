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

namespace DreamCommerce\Component\ShopAppstore\Tests\Billing;

use DreamCommerce\Component\Common\Http\ClientInterface;
use DreamCommerce\Component\ShopAppstore\Api\Criteria;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClient;
use DreamCommerce\Component\ShopAppstore\Api\Http\ShopClientInterface;
use PHPUnit\Framework\TestCase;

class ShopClientTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    private $psrClient;

    /**
     * @var Criteria
     */
    private $shopClient;

    public function setUp(): void
    {
        $this->psrClient = $this->getMockBuilder(ClientInterface::class)->getMock();
        $this->shopClient = new ShopClient($this->psrClient);
    }

    public function testShouldImplements(): void
    {
        $this->assertInstanceOf(ShopClientInterface::class, $this->shopClient);
    }
}