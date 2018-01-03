<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Doctrine\DBAL\Types;

use DreamCommerce\Component\Common\Doctrine\DBAL\Types\EnumType;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;

final class ShopStateEnumType extends EnumType
{
    const TYPE_NAME = 'dc_appstore_shop_state_enum';

    /**
     * @var string
     */
    protected $name = self::TYPE_NAME;

    /**
     * @var array
     */
    protected $values = array(
        OAuthShopInterface::STATE_NEW,
        OAuthShopInterface::STATE_UNINSTALLED,
        OAuthShopInterface::STATE_PREFETCH_TOKENS,
        OAuthShopInterface::STATE_REJECTED_AUTH_CODE,
        OAuthShopInterface::STATE_INSTALLED,
    );
}
