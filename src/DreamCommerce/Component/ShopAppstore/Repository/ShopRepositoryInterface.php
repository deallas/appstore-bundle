<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Repository;

use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ShopRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ApplicationInterface $application
     * @return ShopInterface[]
     */
    public function findByApplication(ApplicationInterface $application): array;
}
