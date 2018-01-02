<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Doctrine\DBAL\Types;

use DreamCommerce\Component\Common\Doctrine\DBAL\Types\MapEnumType;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

final class ShopStateSmallIntType extends MapEnumType
{
    const TYPE_NAME = 'dc_appstore_shop_state_smallint';

    /**
     * @var string
     */
    protected $enumType = self::TYPE_UINT16;

    /**
     * @var string
     */
    protected $name = self::TYPE_NAME;

    /**
     * @var array
     */
    protected $values = array(
        ShopInterface::STATE_NEW => 1,
        ShopInterface::STATE_UNINSTALLED => 2,
        ShopInterface::STATE_PREFETCH_TOKENS => 3,
        ShopInterface::STATE_REJECTED_AUTH_CODE => 4,
        ShopInterface::STATE_INSTALLED => 5,
    );
}
