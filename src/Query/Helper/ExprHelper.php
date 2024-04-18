<?php

declare(strict_types=1);

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
    public function func(callable $function): FuncExpr
    {
        return new FuncExpr($function);
    }

    public function comparison(
        string|FieldValueInterface|callable $field,
        string $operator,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, $operator, $value, $caseSensitive);
    }

    public function eq(
        string|FieldValueInterface|callable $field,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
        bool $strict = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, $strict ? ExprOperator::EQSTRICT : ExprOperator::EQ, $value, $caseSensitive);
    }

    public function neq(
        string|FieldValueInterface|callable $field,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
        bool $strict = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, $strict ? ExprOperator::NEQSTRICT : ExprOperator::NEQ, $value, $caseSensitive);
    }

    public function gt(
        string|FieldValueInterface|callable $field,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::GT, $value, $caseSensitive);
    }

    public function gte(
        string|FieldValueInterface|callable $field,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::GTE, $value, $caseSensitive);
    }

    public function lt(
        string|FieldValueInterface|callable $field,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::LT, $value, $caseSensitive);
    }

    public function lte(
        string|FieldValueInterface|callable $field,
        string|int|float|bool|FieldValueInterface|callable|null $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::LTE, $value, $caseSensitive);
    }

    public function contains(
        string|FieldValueInterface|callable $field,
        string $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::CONTAINS, $value, $caseSensitive);
    }

    public function ends(
        string|FieldValueInterface|callable $field,
        string $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::ENDS, $value, $caseSensitive);
    }

    public function begins(
        string|FieldValueInterface|callable $field,
        string $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::BEGINS, $value, $caseSensitive);
    }

    public function like(
        string|FieldValueInterface|callable $field,
        string $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::LIKE, $value, $caseSensitive);
    }

    public function notlike(
        string|FieldValueInterface|callable $field,
        string $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::NLIKE, $value, $caseSensitive);
    }

    public function match(string|FieldValueInterface|callable $field, string $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, ExprOperator::MATCH, $regexp);
    }

    public function notmatch(string|FieldValueInterface|callable $field, string $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, ExprOperator::NMATCH, $regexp);
    }

    /**
     * @param array<mixed> $value
     */
    public function in(
        string|FieldValueInterface|callable $field,
        array $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::IN, $value, $caseSensitive);
    }

    /**
     * @param array<mixed> $value
     */
    public function notin(
        string|FieldValueInterface|callable $field,
        array $value,
        bool $caseSensitive = true,
    ): ComparisonExpr {
        return new ComparisonExpr($field, ExprOperator::NIN, $value, $caseSensitive);
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
