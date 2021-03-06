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
use Arnapou\PFDB\Query\Field\FieldValueInterface;

/**
 * Signature of the callables :
 * <pre>
 * function(array $row, int|string|null $key = null): string|int|float|bool|null|array {
 *     // compute $value
 *     return $value;
 * }
 * </pre>.
 */
class ExprHelper
{
    public const EQ = '==';
    public const EQSTRICT = '===';
    public const NEQ = '!=';
    public const NEQSTRICT = '!==';
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

    public const OPERATORS = [
        self::EQ,
        self::NEQ,
        self::EQSTRICT,
        self::NEQSTRICT,
        self::GT,
        self::GTE,
        self::LT,
        self::LTE,
        self::LIKE,
        self::NLIKE,
        self::MATCH,
        self::NMATCH,
        self::ENDS,
        self::BEGINS,
        self::CONTAINS,
        self::IN,
        self::NIN,
    ];

    public function func(callable $function): FuncExpr
    {
        return new FuncExpr($function);
    }

    public function comparison(
        string | FieldValueInterface | callable $field,
        string $operator,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, $operator, $value, $caseSensitive);
    }

    public function eq(
        string | FieldValueInterface | callable $field,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true,
        bool $strict = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, $strict ? self::EQSTRICT : self::EQ, $value, $caseSensitive);
    }

    public function neq(
        string | FieldValueInterface | callable $field,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true,
        bool $strict = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, $strict ? self::NEQSTRICT : self::NEQ, $value, $caseSensitive);
    }

    public function gt(
        string | FieldValueInterface | callable $field,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, self::GT, $value, $caseSensitive);
    }

    public function gte(
        string | FieldValueInterface | callable $field,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, self::GTE, $value, $caseSensitive);
    }

    public function lt(
        string | FieldValueInterface | callable $field,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, self::LT, $value, $caseSensitive);
    }

    public function lte(
        string | FieldValueInterface | callable $field,
        string | int | float | bool | null | FieldValueInterface | callable $value,
        bool $caseSensitive = true
    ): ComparisonExpr {
        return new ComparisonExpr($field, self::LTE, $value, $caseSensitive);
    }

    public function contains(string | FieldValueInterface | callable $field, string $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::CONTAINS, $value, $caseSensitive);
    }

    public function ends(string | FieldValueInterface | callable $field, string $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::ENDS, $value, $caseSensitive);
    }

    public function begins(string | FieldValueInterface | callable $field, string $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::BEGINS, $value, $caseSensitive);
    }

    public function like(string | FieldValueInterface | callable $field, string $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::LIKE, $value, $caseSensitive);
    }

    public function notlike(string | FieldValueInterface | callable $field, string $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NLIKE, $value, $caseSensitive);
    }

    public function match(string | FieldValueInterface | callable $field, string $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, self::MATCH, $regexp);
    }

    public function notmatch(string | FieldValueInterface | callable $field, string $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NMATCH, $regexp);
    }

    public function in(string | FieldValueInterface | callable $field, array $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::IN, $value, $caseSensitive);
    }

    public function notin(string | FieldValueInterface | callable $field, array $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, self::NIN, $value, $caseSensitive);
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
