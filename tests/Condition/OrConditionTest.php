<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Condition;

use Arnapou\PFDB\Condition\Operator\EqualOperator;
use Arnapou\PFDB\Condition\Operator\GreaterThanOrEqualOperator;
use Arnapou\PFDB\Condition\Operator\LowerThanOperator;
use Arnapou\PFDB\Condition\OrCondition;
use Arnapou\PFDB\Tests\TestCase;

class OrConditionTest extends TestCase
{
    public function testInteger()
    {
        $operator = new OrCondition([
            new GreaterThanOrEqualOperator('test', 200),
            new LowerThanOperator('test', 100),
        ]);

        $this->assertTrue($operator->match(null, ['test' => 200]));
        $this->assertTrue($operator->match(null, ['test' => 1000]));
        $this->assertTrue($operator->match(null, ['test' => 99]));
        $this->assertFalse($operator->match(null, ['test' => 199]));
        $this->assertFalse($operator->match(null, ['test' => 100]));
    }

    public function testString()
    {
        $operator = new OrCondition([
            new EqualOperator('test', 'abc'),
            new EqualOperator('test', 'def'),
        ]);

        $this->assertTrue($operator->match(null, ['test' => 'abc']));
        $this->assertTrue($operator->match(null, ['test' => 'def']));
        $this->assertFalse($operator->match(null, ['test' => 'aBc']));
        $this->assertFalse($operator->match(null, ['test' => '123']));


        $operator = new OrCondition([
            new EqualOperator('test', 'abc', false),
            new EqualOperator('test', 'def'),
        ]);

        $this->assertTrue($operator->match(null, ['test' => 'aBc']));
        $this->assertFalse($operator->match(null, ['test' => 'dEf']));
    }
}
