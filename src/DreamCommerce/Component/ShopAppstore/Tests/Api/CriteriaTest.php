<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Tests\Billing;

use DreamCommerce\Component\ShopAppstore\Api\Criteria;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    public function testCreate() : void
    {
        $criteria = Criteria::create();
        self::assertInstanceOf(Criteria::class, $criteria);
    }
}