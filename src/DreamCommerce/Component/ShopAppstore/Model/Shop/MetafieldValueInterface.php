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

use DreamCommerce\Component\ShopAppstore\Model\DiscriminatorMappingInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MetafieldValueInterface extends ResourceInterface, DiscriminatorMappingInterface
{
    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @return MetafieldInterface|null
     */
    public function getMetafield(): ?MetafieldInterface;

    /**
     * @param MetafieldInterface $metafield
     */
    public function setMetafield(MetafieldInterface $metafield): void;

    /**
     * @return int|null
     */
    public function getExternalObjectId(): ?int;

    /**
     * @param int $id
     */
    public function setExternalObjectId(int $id): void;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value): void;
}