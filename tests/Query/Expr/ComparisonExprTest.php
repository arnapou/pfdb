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

namespace Arnapou\PFDB\Tests\Query\Expr;

use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Exception\InvalidOperatorException;
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Expr\ComparisonExpr;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Helper\ExprOperator;
use Arnapou\PFDB\Query\Helper\FieldsHelperTrait;
use PHPUnit\Framework\TestCase;

class ComparisonExprTest extends TestCase
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    public function testFieldNotAScalar(): void
    {
        $this->expectException(InvalidFieldException::class);
        $expr = new ComparisonExpr(fn ($row, $key) => [], ExprOperator::EQ, null);
        $expr([]);
    }

    public function testValueShouldBeAnArrayOrScalarForIn(): void
    {
        $this->expectException(InvalidValueException::class);
        $expr = new ComparisonExpr(null, ExprOperator::IN, null);
        $expr([]);
    }

    public function testValueShouldBeAnArrayOrScalarForInWithCallable(): void
    {
        $this->expectException(InvalidValueException::class);
        $expr = new ComparisonExpr(null, ExprOperator::IN, fn ($row, $key) => null);
        $expr([]);
    }

    public function testValueShouldNotBeAnArrayWhenNotIn(): void
    {
        $this->expectException(InvalidValueException::class);
        $expr = new ComparisonExpr(null, ExprOperator::EQ, []);
        $expr([]);
    }

    public function testConstructorExceptionOperator(): void
    {
        $this->expectException(InvalidOperatorException::class);
        $expr = new ComparisonExpr('field', '(unknown)', 42);
        $expr(['field' => 42], null);
    }

    public function testContainsEmptyShouldNotRaiseAnException(): void
    {
        $expr = new ComparisonExpr('field', '*', '');
        $expr(['field' => 42], null);
        $this->expectNotToPerformAssertions();
    }

    public function testSwapFieldAndValue(): void
    {
        $expr = new ComparisonExpr(42, '=', new Field('field'));
        self::assertTrue($expr(['field' => 42], null));
    }

    public function testRegexpNonCaseSensitive(): void
    {
        $expr = new ComparisonExpr('field', 'regexp', '/^[a-z]+$/', false);
        self::assertTrue($expr(['field' => 'ABCDEF'], null));
    }

    public function testGetField(): void
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        self::assertSame(66, \call_user_func($expr->getField(), ['field' => 66], null));

        $expr = new ComparisonExpr(42, '=', new Field('field'), false);
        self::assertSame(42, \call_user_func($expr->getField(), ['we dont care the value']));
    }

    public function testGetValue(): void
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        self::assertSame(42, \call_user_func($expr->getValue(), ['we dont care the value']));

        $expr = new ComparisonExpr(42, '=', new Field('field'), false);
        self::assertSame(66, \call_user_func($expr->getValue(), ['field' => 66], null));

        $expr = new ComparisonExpr('field', '=', $this->fields()->value(42), false);
        self::assertSame(42, \call_user_func($expr->getValue(), ['field' => 66], null));
    }

    public function testIsCaseSensitive(): void
    {
        $expr = new ComparisonExpr('field', '=', 42, false);
        self::assertFalse($expr->isCaseSensitive());
    }
}
