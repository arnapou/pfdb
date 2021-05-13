<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Helper;

use Arnapou\PFDB\Query\Expr\AndExpr;
use Arnapou\PFDB\Query\Expr\BoolExpr;
use Arnapou\PFDB\Query\Expr\ComparisonExpr;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Expr\FuncExpr;
use Arnapou\PFDB\Query\Expr\NestedExprInterface;
use Arnapou\PFDB\Query\Expr\NotExpr;
use Arnapou\PFDB\Query\Expr\OrExpr;

class ExprHelper
{
    public const EQ = '==';
    public const NEQ = '!=';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';
    public const LIKE = 'like';
    public const NLIKE = 'not like';
    public const MATCH = 'regexp';
    public const NMATCH = 'not regexp';
    public const ENDS = '$';
    public const BEGINS = '^';
    public const CONTAINS = '*';
    public const IN = 'in';
    public const NIN = 'not in';

    public function func(callable $function): FuncExpr
    {
        return new FuncExpr($function);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function comparison($field, string $operator, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, $operator, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function in($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::IN, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function notin($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NIN, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function contains($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::CONTAINS, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function ends($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::ENDS, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function begins($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::BEGINS, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function eq($field, $value, bool $caseSensitive = true, bool $strict = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::EQ . ($strict ? '=' : ''), $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function neq($field, $value, bool $caseSensitive = true, bool $strict = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NEQ . ($strict ? '=' : ''), $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function gt($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::GT, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function gte($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::GTE, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function lt($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::LT, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function lte($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::LTE, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function like($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::LIKE, $value, $caseSensitive);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     */
    public function notlike($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NLIKE, $value, $caseSensitive);
    }

    /**
     * @param mixed  $field
     * @param string $regexp
     */
    public function match($field, $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, self::MATCH, $regexp);
    }

    /**
     * @param mixed  $field
     * @param string $regexp
     */
    public function notmatch($field, $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NMATCH, $regexp);
    }

    public function bool(bool $bool): BoolExpr
    {
        return new BoolExpr($bool);
    }

    public function not(ExprInterface $expr): NotExpr
    {
        return new NotExpr($expr);
    }

    public function and(ExprInterface ...$exprs): NestedExprInterface
    {
        return new AndExpr(...$exprs);
    }

    public function or(ExprInterface ...$exprs): NestedExprInterface
    {
        return new OrExpr(...$exprs);
    }
}
