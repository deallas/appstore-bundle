<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\BillingInstall;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\ShopBillingTransitions;
use SM\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class BillingInstallResolver implements MessageResolverInterface
{
    /**
     * @var ObjectManager
     */
    private $shopObjectManager;

    /**
     * @var FactoryInterface
     */
    private $billingStateMachineFactory;

    /**
     * @param ObjectManager $shopObjectManager
     * @param FactoryInterface $billingStateMachineFactory
     */
    public function __construct(ObjectManager $shopObjectManager, FactoryInterface $billingStateMachineFactory)
    {
        $this->shopObjectManager = $shopObjectManager;
        $this->billingStateMachineFactory = $billingStateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Message $message): void
    {
        Assert::isInstanceOf($message, BillingInstall::class);

        $shop = $message->getShop();

        $stateMachine = $this->billingStateMachineFactory->get($shop, ShopBillingTransitions::GRAPH);
        $stateMachine->apply(ShopBillingTransitions::TRANSITION_PAY);

        $this->shopObjectManager->persist($shop);
        $this->shopObjectManager->flush();
    }
}