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

use Arnapou\PFDB\Exception\InvalidExprOperatorException;
use Arnapou\PFDB\Exception\InvalidExprValueException;
use Arnapou\PFDB\Query\Expr\ComparisonExpr;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Helper\ExprTrait;
use PHPUnit\Framework\TestCase;

class ComparisonExprTest extends TestCase
{
    use ExprTrait;

    public function testConstructorExceptionField()
    {
        $this->expectException(InvalidExprValueException::class);
        new ComparisonExpr(new \stdClass(), '==', 42);
    }

    public function testConstructorExceptionValue()
    {
        $this->expectException(InvalidExprValueException::class);
        new ComparisonExpr('field', '==', new \stdClass());
    }

    public function testConstructorExceptionOperator()
    {
        $this->expectException(InvalidExprOperatorException::class);
        $expr = new ComparisonExpr('field', '(unknown)', 42);
        $expr(['field' => 42], null);
    }

    public function testContainsEmptyShouldNotRaiseException()
    {
        $expr = new ComparisonExpr('field', '*', '');
        $expr(['field' => 42], null);
        $this->assertTrue(true);
    }

    public function testInvertFields()
    {
        $expr = new ComparisonExpr(42, '=', new Field('field'));
        $this->assertTrue($expr(['field' => 42], null));
    }

    public function testRegexpNonCaseSensitive()
    {
        $expr = new ComparisonExpr('field', 'regexp', '/^[a-z]+$/', false);
        $this->assertTrue($expr(['field' => 'ABCDEF'], null));
    }

    public function testGetField()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        $this->assertSame('field', $expr->getField());
    }

    public function testGetValue()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        $this->assertSame(42, $expr->getValue());
    }

    public function testIsCaseSensitive()
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        $this->assertSame(false, $expr->isCaseSensitive());
    }
}
