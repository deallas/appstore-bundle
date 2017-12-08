<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Factory;

use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\TokenInterface;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ShopFactory implements ShopFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var FactoryInterface
     */
    private $tokenFactory;

    /**
     * @param FactoryInterface $factory
     * @param FactoryInterface $tokenFactory
     */
    public function __construct(FactoryInterface $factory, FactoryInterface $tokenFactory)
    {
        $this->factory = $factory;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): ShopInterface
    {
        /** @var ShopInterface $object */
        $object = $this->factory->createNew();
        $object->setState(ShopInterface::STATE_NEW);
        $object->setBillingState(ShopInterface::STATE_BILLING_UNPAID);
        $object->setVersion(0);

        /** @var TokenInterface $token */
        $token = $this->tokenFactory->createNew();
        $object->setToken($token);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function createNewByApplicationAndUri(ApplicationInterface $application, UriInterface $uri): ShopInterface
    {
        $object = $this->createNew();
        $object->setUri($uri);
        $object->setApplication($application);

        return $object;
    }
}
