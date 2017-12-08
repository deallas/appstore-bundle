<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Tests\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Uninstall;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\UninstallResolver;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\ShopTransitions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;

class UninstallResolverTest extends TestCase
{
    /**
     * @var FactoryInterface|MockObject
     */
    protected $shopStateMachineFactory;

    /**
     * @var UninstallResolver
     */
    protected $resolver;

    public function setUp()
    {
        $this->shopStateMachineFactory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $this->resolver = new UninstallResolver($this->shopStateMachineFactory);
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
     * @dataProvider stateMessages
     *
     * @param Uninstall $message
     * @param string $transition
     */
    public function testChangeStateWhileResolving(Uninstall $message, string $transition)
    {
        $this->shopStateMachineFactory
            ->expects($this->once())
            ->method('get')
            ->will($this->returnCallback(function() use($transition) {
                $stateMachine = $this->getMockBuilder(StateMachineInterface::class)->getMock();
                $stateMachine
                    ->expects($this->once())
                    ->method('apply')
                    ->will($this->returnCallback(function($fTransition) use($transition) {
                        $this->assertEquals($transition, $fTransition);
                    }));

                return $stateMachine;
            }));
        ;

        $this->resolver->resolve($message);
    }

    /* --------------------------------------------------------------------- */

    public function stateMessages()
    {
        /** @var ApplicationInterface $application */
        $application = $this->getMockBuilder(ApplicationInterface::class)->getMock();

        $messages = [];
        $map = [
            ShopInterface::STATE_PREFETCH_TOKENS => ShopTransitions::TRANSITION_CANCEL_DOWNLOAD_TOKENS,
            ShopInterface::STATE_REJECTED_AUTH_CODE => ShopTransitions::TRANSITION_GIVE_UP,
            ShopInterface::STATE_INSTALLED => ShopTransitions::TRANSITION_UNINSTALL
        ];

        foreach($map as $state => $transition) {
            $shop = $this->getMockBuilder(ShopInterface::class)->getMock();
            $shop->method('getState')
                ->willReturn($state);

            $messages[] = [ new Uninstall($application, $shop), $transition ];
        }

        return $messages;
    }
}