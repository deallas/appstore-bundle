<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Tests\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Upgrade;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\UpgradeResolver;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use PHPUnit\Framework\TestCase;

class UpgradeResolverTest extends TestCase
{
    /**
     * @var UpgradeResolver
     */
    protected $resolver;

    public function setUp()
    {
        $this->resolver = new UpgradeResolver();
    }

    public function testShouldImplements()
    {
        $this->assertInstanceOf(MessageResolverInterface::class, $this->resolver);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentWhileResolve()
    {
        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->resolver->resolve($message);
    }

    /**
     * @dataProvider validMessages
     *
     * @param Upgrade $message
     */
    public function testValidResolve(Upgrade $message)
    {
        $lastVersion = $message->getShop()->getVersion();

        $this->resolver->resolve($message);
        $this->assertTrue($message->getShop()->getVersion() >= $lastVersion);
    }

    public function validMessages()
    {
        /** @var ApplicationInterface $application */
        $application = $this->getMockBuilder(ApplicationInterface::class)->getMock();
        $currentVersion = time();

        $shop1 = $this->getMockBuilder(ShopInterface::class)->getMock();
        $shop1->expects($this->any())
            ->method('getVersion')
            ->willReturn($currentVersion)
        ;
        $shop1->expects($this->never())
            ->method('setVersion')
        ;

        $shop2 = $this->getMockBuilder(ShopInterface::class)->getMock();
        $shop2->expects($this->any())
            ->method('getVersion')
            ->willReturn($currentVersion)
        ;
        $shop2->expects($this->never())
            ->method('setVersion')
        ;

        $shop3 = $this->getMockBuilder(ShopInterface::class)->getMock();
        $shop3->expects($this->any())
            ->method('getVersion')
            ->willReturn($currentVersion)
        ;
        $shop3->expects($this->once())
            ->method('setVersion')
        ;

        return [
            [ new Upgrade($application, $shop1, [ 'application_version' => $currentVersion - 10 ]) ],
            [ new Upgrade($application, $shop2, [ 'application_version' => $currentVersion ]) ],
            [ new Upgrade($application, $shop3, [ 'application_version' => $currentVersion + 10 ]) ]
        ];
    }
}