<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Install;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\ShopTransitions;
use SM\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class InstallResolver implements MessageResolverInterface
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
        /** @var Install $message */
        Assert::isInstanceOf($message, Install::class);

        $shop = $message->getShop();
        $appVersion = $message->getApplicationVersion();

        if($appVersion > $shop->getVersion()) {
            $shop->setVersion($appVersion);
        }

        $state = $shop->getState();

        $stateMachine = $this->shopStateMachineFactory->get($shop, ShopTransitions::GRAPH);
        if($state === ShopInterface::STATE_NEW) {
            $stateMachine->apply(ShopTransitions::TRANSITION_ENQUEUE_DOWNLOAD_TOKENS);
        } elseif($state === ShopInterface::STATE_PREFETCH_TOKENS) {
            $stateMachine->apply(ShopTransitions::TRANSITION_RETRY_DOWNLOAD_TOKENS);
        } elseif($state === ShopInterface::STATE_UNINSTALLED) {
            $stateMachine->apply(ShopTransitions::TRANSITION_REINSTALL);
        }

        $this->shopObjectManager->persist($shop);
        $this->shopObjectManager->flush();
    }
}