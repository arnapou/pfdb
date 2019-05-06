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

use Arnapou\PFDB\Exception\InvalidExprFieldException;
use Arnapou\PFDB\Exception\InvalidExprOperatorException;
use Arnapou\PFDB\Exception\InvalidExprValueException;
use Arnapou\PFDB\Query\Expr\ComparisonExpr;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use PHPUnit\Framework\TestCase;

class ComparisonExprTest extends TestCase
{
    use ExprHelperTrait;

    public function test_constructor_exception_field()
    {
        $this->expectException(InvalidExprFieldException::class);
        new ComparisonExpr(new \stdClass(), '==', 42);
    }

    public function test_constructor_exception_value()
    {
        $this->expectException(InvalidExprValueException::class);
        new ComparisonExpr('field', '==', new \stdClass());
    }

    public function test_constructor_exception_operator()
    {
        $this->expectException(InvalidExprOperatorException::class);
        $expr = new ComparisonExpr('field', '(unknown)', 42);
        $expr(['field' => 42], null);
    }

    public function test_contains_empty_should_not_raise_an_exception()
    {
        $expr = new ComparisonExpr('field', '*', '');
        $expr(['field' => 42], null);
        $this->assertTrue(true);
    }

    public function test_swap_field_and_value()
    {
        $expr = new ComparisonExpr(42, '=', new Field('field'));
        $this->assertTrue($expr(['field' => 42], null));
    }

    public function test_regexp_non_case_sensitive()
    {
        $expr = new ComparisonExpr('field', 'regexp', '/^[a-z]+$/', false);
        $this->assertTrue($expr(['field' => 'ABCDEF'], null));
    }

    public function test_get_field()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        $this->assertIsCallable($expr->getField());
        $this->assertSame(66, \call_user_func($expr->getField(), ['field' => 66], null));

        $expr = new ComparisonExpr(42, '=', new Field('field'), false);
        $this->assertSame(42, \call_user_func($expr->getField(), ['we dont care the value']));
    }

    public function test_get_value()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        $this->assertIsCallable($expr->getValue());
        $this->assertSame(42, \call_user_func($expr->getValue(), ['we dont care the value']));

        $expr = new ComparisonExpr(42, '=', new Field('field'), false);
        $this->assertSame(66, \call_user_func($expr->getValue(), ['field' => 66], null));
    }

    public function test_is_case_sensitive()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        $this->assertSame(false, $expr->isCaseSensitive());
    }
}
