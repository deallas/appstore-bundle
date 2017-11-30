<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Uninstall;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\ShopTransitions;
use SM\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class UninstallResolver implements MessageResolverInterface
{
    /**
     * @var ObjectManager
     */
    private $shopObjectManager;

    /**
     * @var FactoryInterface
     */
    private $shopStateMachineFactory;

    /**
     * @param ObjectManager $shopObjectManager
     * @param FactoryInterface $shopStateMachineFactory
     */
    public function __construct(ObjectManager $shopObjectManager, FactoryInterface $shopStateMachineFactory)
    {
        $this->shopObjectManager = $shopObjectManager;
        $this->shopStateMachineFactory = $shopStateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Message $message): void
    {
        Assert::isInstanceOf($message, Uninstall::class);

        $shop = $message->getShop();

        $stateMachine = $this->shopStateMachineFactory->get($shop, ShopTransitions::GRAPH);
        if($shop->getState() === ShopInterface::STATE_PREFETCH_TOKENS) {
            $stateMachine->apply(ShopTransitions::TRANSITION_CANCEL_DOWNLOAD_TOKENS);
        } else {
            $stateMachine->apply(ShopTransitions::TRANSITION_UNINSTALL);
        }

        $this->shopObjectManager->persist($shop);
        $this->shopObjectManager->flush();
    }
}