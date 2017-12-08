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

namespace DreamCommerce\Component\ShopAppstore\Repository;

use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ShopRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ApplicationInterface $application
     *
     * @return ShopInterface[]|iterable
     */
    public function findByApplication(ApplicationInterface $application): iterable;

    /**
     * @param string $name
     *
     * @return ShopInterface|null
     */
    public function findOneByName(string $name): ?ShopInterface;

    /**
     * @param string $name
     * @param ApplicationInterface $application
     *
     * @return ShopInterface|null
     */
    public function findOneByNameAndApplication(string $name, ApplicationInterface $application): ?ShopInterface;
}
