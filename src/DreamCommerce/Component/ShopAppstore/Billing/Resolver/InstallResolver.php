<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing\Resolver;

use DreamCommerce\Component\ShopAppstore\Billing\Payload\Install;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\ShopTransitions;
use SM\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class InstallResolver implements MessageResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $shopStateMachineFactory;

    /**
     * @param FactoryInterface $shopStateMachineFactory
     */
    public function __construct(FactoryInterface $shopStateMachineFactory)
    {
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

        $stateMachine = $this->shopStateMachineFactory->get($shop, ShopTransitions::GRAPH);
        switch($shop->getState()) {
            case ShopInterface::STATE_NEW:
                $transition = ShopTransitions::TRANSITION_ENQUEUE_DOWNLOAD_TOKENS;
                break;
            case ShopInterface::STATE_PREFETCH_TOKENS:
                $transition = ShopTransitions::TRANSITION_RETRY_DOWNLOAD_TOKENS;
                break;
            case ShopInterface::STATE_REJECTED_AUTH_CODE:
                $transition = ShopTransitions::TRANSITION_REFRESH_AUTH_CODE;
                break;
            case ShopInterface::STATE_UNINSTALLED:
                $transition = ShopTransitions::TRANSITION_REINSTALL;
                break;
            default:
                throw UnableDispatchException::forUnsupportedShopState($shop, $message);
        }

        $stateMachine->apply($transition);
    }
}