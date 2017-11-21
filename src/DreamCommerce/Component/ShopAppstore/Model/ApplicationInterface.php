<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

interface ApplicationInterface
{
    public function getId(): string;

    public function getSecret(): string;

    public function getAppStoreSecret(): string;
}