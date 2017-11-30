<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Factory;

use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ShopFactoryInterface extends FactoryInterface
{
    /**
     * @param ApplicationInterface $application
     * @param UriInterface $uri
     * @return ShopInterface
     */
    public function createNewByApplicationAndUri(ApplicationInterface $application, UriInterface $uri): ShopInterface;
}