<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api\Resource;

use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;

interface IdentifierAwareInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getIdentifierName(): string;
}