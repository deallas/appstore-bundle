<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

class Application implements ApplicationInterface
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $appstoreSecret;

    /**
     * {@inheritdoc}
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppstoreSecret(): string
    {
        return $this->appstoreSecret;
    }


}