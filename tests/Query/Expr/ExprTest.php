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

use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use PHPUnit\Framework\TestCase;

class ExprTest extends TestCase
{
    use ExprHelperTrait;

    public function test_EQ()
    {
        $this->assertTrue(\call_user_func($this->expr()->eq('test', 'abc'), ['test' => 'abc']));
        $this->assertFalse(\call_user_func($this->expr()->eq('test', 'abc'), ['test' => 'aBc']));
        $this->assertFalse(\call_user_func($this->expr()->eq('test', 123), ['test' => '123']));
        $this->assertTrue(\call_user_func($this->expr()->eq('test', 123, true, false), ['test' => '123']));
        $this->assertTrue(\call_user_func($this->expr()->eq('test', 'abc', false, true), ['test' => 'aBc']));
    }

    public function test_NEQ()
    {
        $this->assertFalse(\call_user_func($this->expr()->neq('test', 'abc'), ['test' => 'abc']));
        $this->assertTrue(\call_user_func($this->expr()->neq('test', 'abc'), ['test' => 'aBc']));
        $this->assertTrue(\call_user_func($this->expr()->neq('test', 123), ['test' => '123']));
        $this->assertFalse(\call_user_func($this->expr()->neq('test', 123, true, false), ['test' => '123']));
        $this->assertFalse(\call_user_func($this->expr()->neq('test', 'abc', false, true), ['test' => 'aBc']));
    }

    public function test_GT()
    {
        $this->assertFalse(\call_user_func($this->expr()->gt('test', 'def'), ['test' => 'abc']));
        $this->assertTrue(\call_user_func($this->expr()->gt('test', 'BBB'), ['test' => 'bbb']));
        $this->assertFalse(\call_user_func($this->expr()->gt('test', 'BBB', false), ['test' => 'bbb']));
        $this->assertFalse(\call_user_func($this->expr()->gt('test', 123), ['test' => '123']));
        $this->assertTrue(\call_user_func($this->expr()->gt('test', 123), ['test' => '124']));
        $this->assertTrue(\call_user_func($this->expr()->gt('test', 123, false), ['test' => 200.5]));
    }

    public function test_GTE()
    {
        $this->assertFalse(\call_user_func($this->expr()->gte('test', 'def'), ['test' => 'abc']));
        $this->assertTrue(\call_user_func($this->expr()->gte('test', 'BBB'), ['test' => 'bbb']));
        $this->assertTrue(\call_user_func($this->expr()->gte('test', 'BBB', false), ['test' => 'bbb']));
        $this->assertTrue(\call_user_func($this->expr()->gte('test', 123), ['test' => '123']));
        $this->assertFalse(\call_user_func($this->expr()->gte('test', 123), ['test' => '100']));
        $this->assertTrue(\call_user_func($this->expr()->gte('test', 123, false), ['test' => 200.5]));
    }

    public function test_LT()
    {
        $this->assertTrue(\call_user_func($this->expr()->lt('test', 'def'), ['test' => 'abc']));
        $this->assertTrue(\call_user_func($this->expr()->lt('test', 'bbb'), ['test' => 'BBB']));
        $this->assertFalse(\call_user_func($this->expr()->lt('test', 'bbb', false), ['test' => 'bbb']));
        $this->assertFalse(\call_user_func($this->expr()->lt('test', 123), ['test' => '123']));
        $this->assertTrue(\call_user_func($this->expr()->lt('test', 123), ['test' => '122.9']));
        $this->assertFalse(\call_user_func($this->expr()->lt('test', 123, false), ['test' => 200.5]));
    }

    public function test_LTE()
    {
        $this->assertTrue(\call_user_func($this->expr()->lte('test', 'def'), ['test' => 'abc']));
        $this->assertTrue(\call_user_func($this->expr()->lte('test', 'bbb'), ['test' => 'BBB']));
        $this->assertTrue(\call_user_func($this->expr()->lte('test', 'bbb', false), ['test' => 'bbb']));
        $this->assertTrue(\call_user_func($this->expr()->lte('test', 123), ['test' => '123']));
        $this->assertTrue(\call_user_func($this->expr()->lte('test', 123), ['test' => '122.9']));
        $this->assertFalse(\call_user_func($this->expr()->lte('test', 123, false), ['test' => 200.5]));
    }

    public function test_LIKE()
    {
        $this->assertTrue(\call_user_func($this->expr()->like('test', '%lo_w%'), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->like('test', '%loz%'), ['test' => 'hello world']));
    }

    public function test_NOTLIKE()
    {
        $this->assertFalse(\call_user_func($this->expr()->notlike('test', '%lo_w%'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->notlike('test', '%loz%'), ['test' => 'hello world']));
    }

    public function test_MATCH()
    {
        $this->assertTrue(\call_user_func($this->expr()->match('test', 'lo.w'), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->match('test', '^lo.w'), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->match('test', 'LO.w'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->match('test', '/LO.w/i'), ['test' => 'hello world']));
    }

    public function test_NOTMATCH()
    {
        $this->assertFalse(\call_user_func($this->expr()->notmatch('test', 'lo.w'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->notmatch('test', '^lo.w'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->notmatch('test', 'LO.w'), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->notmatch('test', '/LO.w/i'), ['test' => 'hello world']));
    }

    public function test_BEGINS()
    {
        $this->assertFalse(\call_user_func($this->expr()->begins('test', 'Hell'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->begins('test', 'Hell', false), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->begins('test', 'orld'), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->begins('test', 'oRld', false), ['test' => 'hello world']));
    }

    public function test_ENDS()
    {
        $this->assertFalse(\call_user_func($this->expr()->ends('test', 'Hell'), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->ends('test', 'Hell', false), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->ends('test', 'orld'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->ends('test', 'oRld', false), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->ends('test', 'oRld'), ['test' => 'hello world']));
    }

    public function test_CONTAINS()
    {
        $this->assertTrue(\call_user_func($this->expr()->contains('test', 'lo w'), ['test' => 'hello world']));
        $this->assertTrue(\call_user_func($this->expr()->contains('test', 'lo W', false), ['test' => 'hello world']));
        $this->assertFalse(\call_user_func($this->expr()->contains('test', 'lo W'), ['test' => 'hello world']));
    }

    public function test_IN()
    {
        $this->assertTrue(\call_user_func($this->expr()->in('test', ['a', 'b', 'c']), ['test' => 'b']));
        $this->assertFalse(\call_user_func($this->expr()->in('test', ['a', 'b', 'c']), ['test' => 'z']));
        $this->assertTrue(\call_user_func($this->expr()->in('test', ['a', 'b', 'c'], false), ['test' => 'B']));
        $this->assertFalse(\call_user_func($this->expr()->in('test', ['a', 'b', 'c'], false), ['test' => 'Z']));
    }

    public function test_NOTIN()
    {
        $this->assertFalse(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c']), ['test' => 'b']));
        $this->assertTrue(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c']), ['test' => 'z']));
        $this->assertFalse(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c'], false), ['test' => 'B']));
        $this->assertTrue(\call_user_func($this->expr()->notin('test', ['a', 'b', 'c'], false), ['test' => 'Z']));
    }

    public function test_FUNC()
    {
        $func = function (array $row) {
            return strtolower($row['test']) == 'abc';
        };
        $this->assertFalse(\call_user_func($this->expr()->func($func), ['test' => 'b']));
        $this->assertTrue(\call_user_func($this->expr()->func($func), ['test' => 'ABC']));
    }

    public function test_both_field_and_value_are_callable()
    {
        $left  = function (array $row) {
            return 'a' . $row['left'];
        };
        $right = function (array $row) {
            return $row['right'] . 'z';
        };
        $this->assertFalse(\call_user_func($this->expr()->eq($left, $right), ['left' => 'bcz', 'right' => 'ABC']));
        $this->assertFalse(\call_user_func($this->expr()->eq($left, $right, false), ['left' => 'bcz', 'right' => 'truc']));
        $this->assertTrue(\call_user_func($this->expr()->eq($left, $right), ['left' => 'bcz', 'right' => 'abc']));
        $this->assertTrue(\call_user_func($this->expr()->eq($left, $right, false), ['left' => 'BCZ', 'right' => 'abc']));
    }

    public function test_both_field_and_value_are_field_object()
    {
        $this->assertTrue(\call_user_func($this->expr()->eq(new Field('left'), 'abcd'), ['left' => 'abcd', 'right' => 'abcd']));
        $this->assertTrue(\call_user_func($this->expr()->eq(new Field('left'), new Field('right')), ['left' => 'abcd', 'right' => 'abcd']));
        $this->assertFalse(\call_user_func($this->expr()->eq(new Field('left'), new Field('right')), ['left' => 'abcd', 'right' => 'ABCD']));
    }

    public function test_IN_wrong_value()
    {
        $this->expectException(InvalidValueException::class);
        \call_user_func($this->expr()->in('test', 'not_an_array'), ['test' => 'foo bar']);
    }

    public function test_MATCH_wrong_value()
    {
        $this->expectException(InvalidValueException::class);
        \call_user_func($this->expr()->match('test', ['not', 'a', 'string']), ['test' => 'foo bar']);
    }

    public function test_sanitize_operator()
    {
        $this->assertSame('regexp', $this->expr()->comparison('xx', '~', 'yy')->getOperator());
        $this->assertSame('regexp', $this->expr()->comparison('xx', 'regex', 'yy')->getOperator());
        $this->assertSame('regexp', $this->expr()->comparison('xx', 'match', 'yy')->getOperator());
        $this->assertSame('regexp', $this->expr()->comparison('xx', 'not match', 'yy')->getOperator());
        $this->assertSame('==', $this->expr()->comparison('xx', '=', 'yy')->getOperator());
        $this->assertSame('==', $this->expr()->comparison('xx', 'not =', 'yy')->getOperator());
        $this->assertSame('!=', $this->expr()->comparison('xx', '<>', 'yy')->getOperator());
        $this->assertTrue($this->expr()->comparison('xx', 'not =', 'yy')->isNot());
        $this->assertFalse($this->expr()->comparison('xx', '>', 'yy')->isNot());
    }
}
