<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore;

final class ShopTransitions
{
    public const GRAPH = 'dream_commerce_appstore_shop';

    public const TRANSITION_INSTALL                     = 'install';
    public const TRANSITION_UNINSTALL                   = 'uninstall';
    public const TRANSITION_REINSTALL                   = 'reinstall';
    public const TRANSITION_ENQUEUE_DOWNLOAD_TOKENS     = 'enqueue_download_tokens';
    public const TRANSITION_CANCEL_DOWNLOAD_TOKENS      = 'cancel_download_tokens';
    public const TRANSITION_RETRY_DOWNLOAD_TOKENS       = 'retry_download_tokens';

    private function __construct()
    {
    }
}