<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

interface ApplicationInterface
{
    /**
     * @return string
     */
    public function getAppId(): string;

    /**
     * @return string
     */
    public function getAppSecret(): string;

    /**
     * @return string
     */
    public function getAppstoreSecret(): string;
}