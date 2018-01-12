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

    public function testWhere(): void
    {
        $criteria = new Criteria();
        $this->assertCount(0, $criteria->getWhereExpression());

        $criteria->where('field_1', 'value_1');

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_EQUAL => 'value_1' ]], $expr);
    }

    public function testWhereOperator(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', 'value_1', Criteria::OPERATOR_NOT_EQUAL);

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_NOT_EQUAL => 'value_1' ]], $expr);
    }

    public function testWhereSameField(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', 'value_1');
        $this->assertCount(1, $criteria->getWhereExpression());

        $criteria->where('field_1', 'value_2');

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_EQUAL => 'value_2' ]], $expr);
    }

    public function testWhereReset(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', 'value_1');
        $this->assertCount(1, $criteria->getWhereExpression());

        $criteria->where('field_2', 'value_2');
        $this->assertCount(1, $criteria->getWhereExpression());
    }

    public function testWhereArrayWithEqualOperator(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', [ 5, 10, 15 ]);

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_IN => [ 5, 10, 15 ] ]], $expr);
    }

    public function testWhereArrayWithNotEqualOperator(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', [ 15, 20, 25 ], Criteria::OPERATOR_NOT_EQUAL);

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_NOT_IN => [ 15, 20, 25 ] ]], $expr);
    }

    public function testWhereScalarWithInOperator(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', 5, Criteria::OPERATOR_IN);

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_EQUAL => 5 ]], $expr);
    }

    public function testWhereScalarWithNotInOperator(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', 15, Criteria::OPERATOR_NOT_IN);

        $expr = $criteria->getWhereExpression();
        $this->assertCount(1, $expr);
        $this->assertEquals(['field_1' => [ Criteria::OPERATOR_NOT_EQUAL => 15 ]], $expr);
    }

    public function testAndWhere(): void
    {
        $criteria = new Criteria();
        $criteria->where('field_1', 'value_1');
        $criteria->andWhere('field_2', 'value_2');

        $this->assertCount(2, $criteria->getWhereExpression());

        $criteria->andWhere('field_1', 'value_3');
        $this->assertCount(2, $criteria->getWhereExpression());
    }
}