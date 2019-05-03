<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Condition\Operator;

use Arnapou\PFDB\Condition\Operator\EqualOperator;
use Arnapou\PFDB\Tests\TestCase;

class EqualOperatorTest extends TestCase
{
    public function testInteger()
    {
        $operator = new EqualOperator('test', 123);

        $this->assertTrue($operator->match(null, ['test' => 123]));
        $this->assertTrue($operator->match(null, ['test' => '123']));
        $this->assertFalse($operator->match(null, ['test' => 45]));
    }

    public function testEmpty()
    {
        // '' and NULL are identical for this operator
        $operators = [
            new EqualOperator('test', ''),
            new EqualOperator('test', null),
        ];

        foreach ($operators as $operator) {
            $this->assertTrue($operator->match(null, ['test' => '']));
            $this->assertFalse($operator->match(null, ['test' => null]));
            $this->assertFalse($operator->match(null, ['test' => 0]));
            $this->assertFalse($operator->match(null, ['test' => ' ']));
        }
    }

    public function testFloat()
    {
        $operator = new EqualOperator('test', 1.25);

        $this->assertTrue($operator->match(null, ['test' => 1.25]));
        $this->assertTrue($operator->match(null, ['test' => 1.2500]));
        $this->assertTrue($operator->match(null, ['test' => 5 / 4]));
        $this->assertTrue($operator->match(null, ['test' => '1.25']));
        $this->assertFalse($operator->match(null, ['test' => 2.25]));
    }

    public function testString()
    {
        $operator = new EqualOperator('test', 'abc');

        $this->assertTrue($operator->match(null, ['test' => 'abc']));
        $this->assertFalse($operator->match(null, ['test' => 'aBc']));
        $this->assertFalse($operator->match(null, ['test' => 'abcd']));

        $operator = new EqualOperator('test', 'abc', false);

        $this->assertTrue($operator->match(null, ['test' => 'abc']));
        $this->assertTrue($operator->match(null, ['test' => 'aBc']));
    }

    public function testFieldNull()
    {
        $operator = new EqualOperator(null, 'abc');

        $this->assertTrue($operator->match('abc', 'ignored_in_this_case'));
    }
}
