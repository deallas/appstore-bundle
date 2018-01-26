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

namespace DreamCommerce\Component\ShopAppstore\Model\Shop;

use Doctrine\Common\Collections\Collection;
use DreamCommerce\Component\ShopAppstore\Model\ShopItemInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MetafieldInterface extends ShopItemInterface, ResourceInterface
{
    /**
     * type of integer
     */
    const TYPE_INT = 1;

    /**
     * type of float
     */
    const TYPE_FLOAT = 2;

    /**
     * type of string
     */

    const TYPE_STRING = 3;

    /**
     * type of binary data
     */
    const TYPE_BLOB = 4;

    /**
     * @param string $key
     */
    public function setKey(string $key): void;

    /**
     * @return null|string
     */
    public function getKey(): ?string;

    public function setNamespace(string $namespace): void;
    public function getNamespace();
    public function setDescription(string $description);
    public function getDescription();
    public function setObject($object=null);
    public function getObject();
    public function addMetafieldValue(MetafieldValue $metafieldValues);
    public function removeMetafieldValue(MetafieldValue $metafield);
    public function getMetafieldValues(): Collection;
    public function setType(string $type): void;


    public function getType(): ?string;
}