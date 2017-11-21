<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model;

interface ApplicationDependInterface
{
    /**
     * @return ApplicationInterface|null
     */
    public function getApplication(): ?ApplicationInterface;

    /**
     * @param ApplicationInterface|null $application
     */
    public function setApplication(?ApplicationInterface $application): void;
}