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

use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use PHPUnit\Framework\TestCase;

class ExprTest extends TestCase
{
    use ExprHelperTrait;

    public function testEq(): void
    {
        self::assertTrue(\call_user_func($this->expr()->eq('test', 'abc'), ['test' => 'abc']));
        self::assertFalse(\call_user_func($this->expr()->eq('test', 'abc'), ['test' => 'aBc']));
        self::assertFalse(\call_user_func($this->expr()->eq('test', 123), ['test' => '123']));
        self::assertTrue(\call_user_func($this->expr()->eq('test', 123, true, false), ['test' => '123']));
        self::assertTrue(\call_user_func($this->expr()->eq('test', 'abc', false, true), ['test' => 'aBc']));
    }

    public function testNeq(): void
    {
        self::assertFalse(\call_user_func($this->expr()->neq('test', 'abc'), ['test' => 'abc']));
        self::assertTrue(\call_user_func($this->expr()->neq('test', 'abc'), ['test' => 'aBc']));
        self::assertTrue(\call_user_func($this->expr()->neq('test', 123), ['test' => '123']));
        self::assertFalse(\call_user_func($this->expr()->neq('test', 123, true, false), ['test' => '123']));
        self::assertFalse(\call_user_func($this->expr()->neq('test', 'abc', false, true), ['test' => 'aBc']));
    }

    public function testGt(): void
    {
        self::assertFalse(\call_user_func($this->expr()->gt('test', 'def'), ['test' => 'abc']));
        self::assertTrue(\call_user_func($this->expr()->gt('test', 'BBB'), ['test' => 'bbb']));
        self::assertFalse(\call_user_func($this->expr()->gt('test', 'BBB', false), ['test' => 'bbb']));
        self::assertFalse(\call_user_func($this->expr()->gt('test', 123), ['test' => '123']));
        self::assertTrue(\call_user_func($this->expr()->gt('test', 123), ['test' => '124']));
        self::assertTrue(\call_user_func($this->expr()->gt('test', 123, false), ['test' => 200.5]));
    }

    public function testGte(): void
    {
        self::assertFalse(\call_user_func($this->expr()->gte('test', 'def'), ['test' => 'abc']));
        self::assertTrue(\call_user_func($this->expr()->gte('test', 'BBB'), ['test' => 'bbb']));
        self::assertTrue(\call_user_func($this->expr()->gte('test', 'BBB', false), ['test' => 'bbb']));
        self::assertTrue(\call_user_func($this->expr()->gte('test', 123), ['test' => '123']));
        self::assertFalse(\call_user_func($this->expr()->gte('test', 123), ['test' => '100']));
        self::assertTrue(\call_user_func($this->expr()->gte('test', 123, false), ['test' => 200.5]));
    }

    public function testLt(): void
    {
        self::assertTrue(\call_user_func($this->expr()->lt('test', 'def'), ['test' => 'abc']));
        self::assertTrue(\call_user_func($this->expr()->lt('test', 'bbb'), ['test' => 'BBB']));
        self::assertFalse(\call_user_func($this->expr()->lt('test', 'bbb', false), ['test' => 'bbb']));
        self::assertFalse(\call_user_func($this->expr()->lt('test', 123), ['test' => '123']));
        self::assertTrue(\call_user_func($this->expr()->lt('test', 123), ['test' => '122.9']));
        self::assertFalse(\call_user_func($this->expr()->lt('test', 123, false), ['test' => 200.5]));
    }

    public function testLte(): void
    {
        self::assertTrue(\call_user_func($this->expr()->lte('test', 'def'), ['test' => 'abc']));
        self::assertTrue(\call_user_func($this->expr()->lte('test', 'bbb'), ['test' => 'BBB']));
        self::assertTrue(\call_user_func($this->expr()->lte('test', 'bbb', false), ['test' => 'bbb']));
        self::assertTrue(\call_user_func($this->expr()->lte('test', 123), ['test' => '123']));
        self::assertTrue(\call_user_func($this->expr()->lte('test', 123), ['test' => '122.9']));
        self::assertFalse(\call_user_func($this->expr()->lte('test', 123, false), ['test' => 200.5]));
    }

    public function testLike(): void
    {
        self::assertTrue(\call_user_func($this->expr()->like('test', '%lo_w%'), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->like('test', '%loz%'), ['test' => 'hello world']));
    }

    public function testNotLike(): void
    {
        self::assertFalse(\call_user_func($this->expr()->notlike('test', '%lo_w%'), ['test' => 'hello world']), '%lo_w%');
        self::assertTrue(\call_user_func($this->expr()->notlike('test', '%loz%'), ['test' => 'hello world']), '%loz%');
    }

    public function testMatch(): void
    {
        self::assertTrue(\call_user_func($this->expr()->match('test', 'lo.w'), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->match('test', '^lo.w'), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->match('test', 'LO.w'), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->match('test', '/LO.w/i'), ['test' => 'hello world']));
    }

    public function testNotMatch(): void
    {
        self::assertFalse(\call_user_func($this->expr()->notmatch('test', 'lo.w'), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->notmatch('test', '^lo.w'), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->notmatch('test', 'LO.w'), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->notmatch('test', '/LO.w/i'), ['test' => 'hello world']));
    }

    public function testBegins(): void
    {
        self::assertFalse(\call_user_func($this->expr()->begins('test', 'Hell'), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->begins('test', 'Hell', false), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->begins('test', 'orld'), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->begins('test', 'oRld', false), ['test' => 'hello world']));
    }

    public function testEnds(): void
    {
        self::assertFalse(\call_user_func($this->expr()->ends('test', 'Hell'), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->ends('test', 'Hell', false), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->ends('test', 'orld'), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->ends('test', 'oRld', false), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->ends('test', 'oRld'), ['test' => 'hello world']));
    }

    public function testContains(): void
    {
        self::assertTrue(\call_user_func($this->expr()->contains('test', 'lo w'), ['test' => 'hello world']));
        self::assertTrue(\call_user_func($this->expr()->contains('test', 'lo W', false), ['test' => 'hello world']));
        self::assertFalse(\call_user_func($this->expr()->contains('test', 'lo W'), ['test' => 'hello world']));
    }

    public function testIn(): void
    {
        self::assertTrue(\call_user_func($this->expr()->in('test', ['a', 'b', 'c']), ['test' => 'b']));
        self::assertFalse(\call_user_func($this->expr()->in('test', ['a', 'b', 'c']), ['test' => 'z']));
        self::assertTrue(\call_user_func($this->expr()->in('test', ['a', 'b', 'c'], false), ['test' => 'B']));
        self::assertFalse(\call_user_func($this->expr()->in('test', ['a', 'b', 'c'], false), ['test' => 'Z']));
    }

    public function testNotIn(): void
    {
        self::assertFalse(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c']), ['test' => 'b']));
        self::assertTrue(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c']), ['test' => 'z']));
        self::assertFalse(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c'], false), ['test' => 'B']));
        self::assertTrue(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c'], false), ['test' => 'Z']));
    }

    public function testFunc(): void
    {
        $func = function (array $row) {
            return 'abc' == strtolower($row['test']);
        };
        self::assertFalse(\call_user_func($this->expr()->func($func), ['test' => 'b']));
        self::assertTrue(\call_user_func($this->expr()->func($func), ['test' => 'ABC']));
    }

    public function testBothFieldAndValueAreCallable(): void
    {
        $left = function (array $row) {
            return 'a' . $row['left'];
        };
        $right = function (array $row) {
            return $row['right'] . 'z';
        };
        self::assertFalse(\call_user_func($this->expr()->eq($left, $right), ['left' => 'bcz', 'right' => 'ABC']));
        self::assertFalse(\call_user_func($this->expr()->eq($left, $right, false), ['left' => 'bcz', 'right' => 'truc']));
        self::assertTrue(\call_user_func($this->expr()->eq($left, $right), ['left' => 'bcz', 'right' => 'abc']));
        self::assertTrue(\call_user_func($this->expr()->eq($left, $right, false), ['left' => 'BCZ', 'right' => 'abc']));
    }

    public function testBothFieldAndValueAreFieldObject(): void
    {
        self::assertTrue(\call_user_func($this->expr()->eq(new Field('left'), 'abcd'), ['left' => 'abcd', 'right' => 'abcd']));
        self::assertTrue(\call_user_func($this->expr()->eq(new Field('left'), new Field('right')), ['left' => 'abcd', 'right' => 'abcd']));
        self::assertFalse(\call_user_func($this->expr()->eq(new Field('left'), new Field('right')), ['left' => 'abcd', 'right' => 'ABCD']));
    }

    public function testSanitizeOperator(): void
    {
        self::assertSame('regexp', $this->expr()->comparison('xx', '~', 'yy')->getOperator()->value);
        self::assertSame('regexp', $this->expr()->comparison('xx', 'regex', 'yy')->getOperator()->value);
        self::assertSame('regexp', $this->expr()->comparison('xx', 'match', 'yy')->getOperator()->value);
        self::assertSame('regexp', $this->expr()->comparison('xx', 'not match', 'yy')->getOperator()->value);
        self::assertSame('==', $this->expr()->comparison('xx', '=', 'yy')->getOperator()->value);
        self::assertSame('==', $this->expr()->comparison('xx', 'not =', 'yy')->getOperator()->value);
        self::assertSame('!=', $this->expr()->comparison('xx', '<>', 'yy')->getOperator()->value);
        self::assertTrue($this->expr()->comparison('xx', 'not =', 'yy')->isNot());
        self::assertFalse($this->expr()->comparison('xx', '>', 'yy')->isNot());
    }
}
