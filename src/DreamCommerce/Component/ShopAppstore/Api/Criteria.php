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

use Psr\Http\Message\RequestInterface;

final class Criteria
{
    const PART_EXPRESSIONS          = 'expressions';
    const PART_ORDERING             = 'ordering';
    const PART_LIMIT                = 'limit';
    const PART_PAGE                 = 'page';

    const OPERATOR_EQUAL            = '=';
    const OPERATOR_NOT_EQUAL        = '!=';
    const OPERATOR_GREATER          = '>';
    const OPERATOR_GREATER_EQUAL    = '>=';
    const OPERATOR_LESS             = '<';
    const OPERATOR_LESS_EQUAL       = '<=';
    const OPERATOR_LIKE             = 'like';
    const OPERATOR_NOT_LIKE         = 'not like';
    const OPERATOR_IN               = 'in';
    const OPERATOR_NOT_IN           = 'not in';

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
    public function where(string $field, $value = null, $operator = self::OPERATOR_EQUAL): self
    {
        $this->expressions = [];
        $this->andWhere($field, $value, $operator);

        return $this;
    }

    /**
     * @param string $field
     * @param array|string $value
     * @param string $operator
     * @return Criteria
     */
    public function andWhere(string $field, $value, $operator = self::OPERATOR_EQUAL): self
    {
        if(!isset($this->expressions[$field])) {
            $this->expressions[$field] = [];
        }

        if(is_array($value)) {
            if($operator === self::OPERATOR_EQUAL) {
                $operator = self::OPERATOR_IN;
            } elseif($operator === self::OPERATOR_NOT_EQUAL) {
                $operator = self::OPERATOR_NOT_IN;
            } elseif(!in_array($operator, [ self::OPERATOR_IN, self::OPERATOR_NOT_IN ])) {
                // TODO throw exception
            }
        } elseif(is_scalar($value)) {
            if($operator === self::OPERATOR_IN) {
                $operator = self::OPERATOR_EQUAL;
            } elseif($operator === self::OPERATOR_NOT_IN) {
                $operator = self::OPERATOR_NOT_EQUAL;
            }
        } else {
            // TODO throw exception
        }

        $this->expressions[$field][$operator] = $value;

        return $this;
    }

    /**
     * @param string|array $expr syntax:
     * <field> (asc|desc)
     * or
     * (+|-)<field>
     * @return self
     * @throws \RuntimeException
     */
    public function orderBy($expr): self
    {
        $expr = (array)$expr;

        foreach($expr as $e) {
            // basic syntax, with asc/desc suffix
            if (preg_match('/([a-z_0-9.]+) (asc|desc)$/i', $e)) {
                $this->orderings[] = $e;
            } else if (preg_match('/([\+\-]?)([a-z_0-9.]+)/i', $e, $matches)) {
                // alternative syntax - with +/- prefix
                $subResult = $matches[2];
                if ($matches[1] == '' || $matches[1] == '+') {
                    $subResult .= ' asc';
                } else {
                    $subResult .= ' desc';
                }
                $this->orderings[] = $subResult;
            } else {
                // TODO throw exception
            }
        }

        return $this;
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
     * @param string $part
     * @return void
     */
    public function reset(string $part = null): void
    {
        if($part === self::PART_LIMIT || $part === null) {
            $this->limit = 50;
        }
        if($part === self::PART_PAGE || $part === null) {
            $this->page = 1;
        }
        if($part === self::PART_EXPRESSIONS || $part === null) {
            $this->expressions = [];
        }
        if($part === self::PART_ORDERING || $part === null) {
            $this->orderings = [];
        }
    }

    /**
     * @param RequestInterface $request
     */
    public function fillRequest(RequestInterface $request): void
    {
        $query = [];
        if(count($this->expressions) > 0) {
            $query['filters'] = $this->expressions;
        }
        if(count($this->orderings) > 0) {
            $query['order'] = $this->orderings;
        }
        if($this->limit !== null) {
            $query['limit'] = $this->limit;
        }
        if($this->page !== null) {
            $query['page'] = $this->page;
        }

        if(count($query) > 0) {
            $uri = $request->getUri();
            $uri = $uri->withQuery(http_build_query($query));

            $request->withUri($uri);
        }
    }
}