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

namespace DreamCommerce\Component\ShopAppstore\Api;

final class Criteria
{
    const OPERATOR_EQUAL        = '=';
    const OPERATOR_NOT_EQUAL    = '!=';

    /**
     * @var array|null
     */
    private $expressions;

    /**
     * @var array|null
     */
    private $orderings;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * Creates an instance of the class.
     *
     * @return Criteria
     */
    public static function create()
    {
        return new static();
    }

    public function __construct()
    {
        $this->reset();
    }

    /**
     * @param string $field
     * @param array|string $value
     * @param string $operator
     * @return self
     */
    public function where(string $field, $value, $operator = self::OPERATOR_EQUAL): self
    {
        $this->expressions = [];
        $this->andWhere($field, $value, $operator);
    }

    /**
     * @param string $field
     * @param array|string $value
     * @param string $operator
     * @return Criteria
     */
    public function andWhere(string $field, $value, $operator = self::OPERATOR_EQUAL): self
    {
        $this->expressions[] = array(
            'field' => $field,
            'value' => $value,
            'operator' => $operator
        );
    }

    /**
     * @param string $expr syntax:
     * <field> (asc|desc)
     * or
     * (+|-)<field>
     * @return self
     * @throws \RuntimeException
     */
    public function orderBy(string $expr): self
    {
        $matches = array();

        $expr = (array)$expr;

        $result = array();

        foreach($expr as $e) {
            // basic syntax, with asc/desc suffix
            if (preg_match('/([a-z_0-9.]+) (asc|desc)$/i', $e)) {
                $result[] = $e;
            } else if (preg_match('/([\+\-]?)([a-z_0-9.]+)/i', $e, $matches)) {

                // alternative syntax - with +/- prefix
                $subResult = $matches[2];
                if ($matches[1] == '' || $matches[1] == '+') {
                    $subResult .= ' asc';
                } else {
                    $subResult .= ' desc';
                }
                $result[] = $subResult;
            } else {
                // something which should never happen but take care [;
                throw new \RuntimeException('Cannot understand ordering expression', ResourceException::ORDER_NOT_SUPPORTED); // TODO
            }
        }

        $this->orderings = $result;
    }

    /**
     * @param int $page
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param int $limit
     * @return self
     */
    public function setMaxResults(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }


    /**
     * Gets the expression attached to this Criteria.
     *
     * @return array|null
     */
    public function getWhereExpression()
    {
        return $this->expressions;
    }
    /**
     * Gets the current orderings of this Criteria.
     *
     * @return string[]
     */
    public function getOrderings()
    {
        return $this->orderings;
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->limit = 50;
        $this->page = 1;
        $this->expressions = null;
        $this->orderings = null;
    }
}