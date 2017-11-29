<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing;

use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use Psr\Http\Message\ServerRequestInterface;

interface DispatcherInterface
{
    /**
     * @param string $action
     * @param MessageResolverInterface $resolver
     */
    public function registerResolver(string $action, MessageResolverInterface $resolver): void;

    /**
     * @param ServerRequestInterface $serverRequest
     */
    public function dispatch(ServerRequestInterface $serverRequest): void;
}