<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use InvalidArgumentException;
use Sylius\Component\Registry\ServiceRegistry;

final class ResolverRegistry extends ServiceRegistry
{
    public function __construct($context = 'service')
    {
        parent::__construct(MessageResolverInterface::class, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier): bool
    {
        if(!in_array($identifier, array_keys(DispatcherInterface::ACTION_PAYLOAD_MAP))) {
            throw new InvalidArgumentException('Action "' . $identifier . '" is not supported');
        }

        return parent::has($identifier);
    }
}