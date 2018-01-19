<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api\Resource;

interface IdentifierAwareInterface
{
    /**
     * @return string
     */
    public function getIdentifierName(): string;
}