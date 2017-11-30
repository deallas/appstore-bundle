<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use DreamCommerce\Component\ShopAppstore\Repository\ShopRepositoryInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class Dispatcher extends ServiceRegistry implements DispatcherInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $applicationRegistry;

    /**
     * @var ShopRepositoryInterface
     */
    private $shopRepository;

    /**
     * @param ServiceRegistryInterface $applicationRegistry
     * @param ShopRepositoryInterface $shopRepository
     */
    public function __construct(ServiceRegistryInterface $applicationRegistry, ShopRepositoryInterface $shopRepository)
    {
        $this->applicationRegistry = $applicationRegistry;
        $this->shopRepository = $shopRepository;

        parent::__construct(MessageResolverInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(ServerRequestInterface $serverRequest): void
    {
        if($serverRequest->getMethod() !== 'POST') {
            return;
        }

        $params = $serverRequest->getParsedBody();

        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier): bool
    {
        if(!in_array($identifier, $this->getAvailableActions())) {
            throw new InvalidArgumentException('Action "' . $identifier . '" is not supported');
        }

        return parent::has($identifier);
    }

    /**
     * {@inheritdoc}
     */
    private function getAvailableActions(): array
    {
        return [
            self::ACTION_BILLING_INSTALL,
            self::ACTION_BILLING_SUBSCRIPTION,
            self::ACTION_INSTALL,
            self::ACTION_UNINSTALL,
            self::ACTION_UPGRADE
        ];
    }
}