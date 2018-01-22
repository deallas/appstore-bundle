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

namespace DreamCommerce\Component\ShopAppstore\Model;

class Item extends DataContainer implements ItemInterface
{
    use ShopDependTrait;

    /**
     * @var int|null
     */
    private $externalId;

    /**
     * @param ShopInterface|null $shop
     * @param int|null $externalId
     * @param array $data
     */
    public function __construct(ShopInterface $shop = null, int $externalId = null, array $data = [])
    {
        $this->shop = $shop;
        $this->externalId = $externalId;

        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function hasExternalId(): bool
    {
        return ($this->externalId !== null);
    }
}