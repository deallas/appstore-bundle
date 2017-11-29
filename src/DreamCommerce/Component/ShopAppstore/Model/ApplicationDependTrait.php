<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

trait ApplicationDependTrait
{
    /**
     * @var ApplicationInterface|null
     */
    protected $application;

    /**
     * @return ApplicationInterface|null
     */
    public function getApplication(): ?ApplicationInterface
    {
        return $this->application;
    }

    /**
     * @param ApplicationInterface|null $application
     */
    public function setApplication(?ApplicationInterface $application): void
    {
        $this->application = $application;
    }
}