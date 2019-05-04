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
use Arnapou\PFDB\Query\Expr\ComparisonExpr;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Expr\FuncExpr;
use Arnapou\PFDB\Query\Expr\NestedExprInterface;
use Arnapou\PFDB\Query\Expr\NotExpr;
use Arnapou\PFDB\Query\Expr\OrExpr;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Field\ForeignField;
use Arnapou\PFDB\Query\Field\KeyField;
use Arnapou\PFDB\Table;

class Expr
{
    const EQ = '==';
    const NEQ = '!=';
    const GT = '>';
    const GTE = '>=';
    const LT = '<';
    const LTE = '<=';
    const LIKE = 'like';
    const NLIKE = 'not like';
    const MATCH = 'regexp';
    const NMATCH = 'not regexp';
    const ENDS = '$';
    const BEGINS = '^';
    const CONTAINS = '*';
    const IN = 'in';
    const NIN = 'not in';

    public function field(string $name): Field
    {
        return new Field($name);
    }

    public function keyField(?string $name = null): KeyField
    {
        return new KeyField($name);
    }

    public function foreignField(string $name, Table $foreignTable, $foreignName = null, $selectAlias = null): ForeignField
    {
        return new ForeignField($name, $foreignTable, $foreignName, $selectAlias);
    }

    public function func(callable $function): FuncExpr
    {
        return new FuncExpr($function);
    }

    public function comparison($field, string $operator, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, $operator, $value, $caseSensitive);
    }

    public function in($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::IN, $value, $caseSensitive);
    }

    public function notin($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::NIN, $value, $caseSensitive);
    }

    public function contains($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::CONTAINS, $value, $caseSensitive);
    }

    public function ends($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::ENDS, $value, $caseSensitive);
    }

    public function begins($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::BEGINS, $value, $caseSensitive);
    }

    public function eq($field, $value, bool $caseSensitive = true, bool $strict = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::EQ . ($strict ? '=' : ''), $value, $caseSensitive);
    }

    public function neq($field, $value, bool $caseSensitive = true, bool $strict = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::NEQ . ($strict ? '=' : ''), $value, $caseSensitive);
    }

    public function gt($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::GT, $value, $caseSensitive);
    }

    public function gte($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::GTE, $value, $caseSensitive);
    }

    public function lt($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::LT, $value, $caseSensitive);
    }

    public function lte($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::LTE, $value, $caseSensitive);
    }

    public function like($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::LIKE, $value, $caseSensitive);
    }

    public function notlike($field, $value, bool $caseSensitive = true): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::NLIKE, $value, $caseSensitive);
    }

    public function match($field, $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::MATCH, $regexp);
    }

    public function notmatch($field, $regexp): ComparisonExpr
    {
        return new ComparisonExpr($field, Expr::NMATCH, $regexp);
    }

    public function not(ExprInterface $expr): NotExpr
    {
        return new NotExpr($expr);
    }

    public function and(ExprInterface...$exprs): NestedExprInterface
    {
        return new AndExpr(...$exprs);
    }

    public function or(ExprInterface...$exprs): NestedExprInterface
    {
        return new OrExpr(...$exprs);
    }
}
