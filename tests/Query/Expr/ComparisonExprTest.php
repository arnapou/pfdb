<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query\Expr;

use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Exception\InvalidOperatorException;
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Expr\ComparisonExpr;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Helper\FieldsHelperTrait;
use PHPUnit\Framework\TestCase;

class ComparisonExprTest extends TestCase
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    public function test_constructor_exception_operator()
    {
        $this->expectException(InvalidOperatorException::class);
        $expr = new ComparisonExpr('field', '(unknown)', 42);
        $expr(['field' => 42], null);
    }

    public function test_contains_empty_should_not_raise_an_exception()
    {
        $expr = new ComparisonExpr('field', '*', '');
        $expr(['field' => 42], null);
        self::assertTrue(true);
    }

    public function test_swap_field_and_value()
    {
        $expr = new ComparisonExpr(42, '=', new Field('field'));
        self::assertTrue($expr(['field' => 42], null));
    }

    public function test_regexp_non_case_sensitive()
    {
        $expr = new ComparisonExpr('field', 'regexp', '/^[a-z]+$/', false);
        self::assertTrue($expr(['field' => 'ABCDEF'], null));
    }

    public function test_get_field()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        self::assertIsCallable($expr->getField());
        self::assertSame(66, \call_user_func($expr->getField(), ['field' => 66], null));

        $expr = new ComparisonExpr(42, '=', new Field('field'), false);
        self::assertSame(42, \call_user_func($expr->getField(), ['we dont care the value']));
    }

    public function test_get_value()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        self::assertIsCallable($expr->getValue());
        self::assertSame(42, \call_user_func($expr->getValue(), ['we dont care the value']));

        $expr = new ComparisonExpr(42, '=', new Field('field'), false);
        self::assertSame(66, \call_user_func($expr->getValue(), ['field' => 66], null));

        $expr = new ComparisonExpr('field', '=', $this->fields()->value(42), false);
        self::assertSame(42, \call_user_func($expr->getValue(), ['field' => 66], null));
    }

    public function test_is_case_sensitive()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        self::assertSame(false, $expr->isCaseSensitive());
    }
}
