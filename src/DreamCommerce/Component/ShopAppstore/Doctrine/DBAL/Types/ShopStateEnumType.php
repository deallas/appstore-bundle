<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Doctrine\DBAL\Types;

use DreamCommerce\Component\Common\Doctrine\DBAL\Types\EnumType;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

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
        ShopInterface::STATE_NEW,
        ShopInterface::STATE_UNINSTALLED,
        ShopInterface::STATE_PREFETCH_TOKENS,
        ShopInterface::STATE_REJECTED_AUTH_CODE,
        ShopInterface::STATE_INSTALLED,
    );
}
