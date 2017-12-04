<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing;

use DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException;
use Psr\Http\Message\ServerRequestInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

interface DispatcherInterface extends ServiceRegistryInterface
{
    public const TIMEZONE                       = 'Europe/Warsaw';

    public const ACTION_BILLING_INSTALL         = 'billing_install';
    public const ACTION_BILLING_SUBSCRIPTION    = 'billing_subscription';
    public const ACTION_INSTALL                 = 'install';
    public const ACTION_UPGRADE                 = 'upgrade';
    public const ACTION_UNINSTALL               = 'uninstall';

    /**
     * @param ServerRequestInterface $serverRequest
     * @throws UnableDispatchException
     */
    public function dispatch(ServerRequestInterface $serverRequest): void;
}