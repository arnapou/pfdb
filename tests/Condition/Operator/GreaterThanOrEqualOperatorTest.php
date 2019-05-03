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

use Arnapou\PFDB\Condition\Operator\GreaterThanOrEqualOperator;
use Arnapou\PFDB\Tests\TestCase;

class GreaterThanOrEqualOperatorTest extends TestCase
{
    public function testInteger()
    {
        $operator = new GreaterThanOrEqualOperator('test', 100);

        $this->assertTrue($operator->match(null, ['test' => 101]));
        $this->assertTrue($operator->match(null, ['test' => 100]));
        $this->assertFalse($operator->match(null, ['test' => 99]));
    }

    public function testFloat()
    {
        $operator = new GreaterThanOrEqualOperator('test', 1.25);

        $this->assertTrue($operator->match(null, ['test' => 1.26]));
        $this->assertTrue($operator->match(null, ['test' => 1.25]));
        $this->assertFalse($operator->match(null, ['test' => 1.24]));
    }

    public function testString()
    {
        $operator = new GreaterThanOrEqualOperator('test', 'ddd');

        $this->assertFalse($operator->match(null, ['test' => 'EEE']));
        $this->assertTrue($operator->match(null, ['test' => 'dee']));
        $this->assertTrue($operator->match(null, ['test' => 'ddd']));
        $this->assertFalse($operator->match(null, ['test' => 'ccc']));

        $operator = new GreaterThanOrEqualOperator('test', 'ddd', false);

        $this->assertTrue($operator->match(null, ['test' => 'EEE']));
        $this->assertTrue($operator->match(null, ['test' => 'dEE']));
        $this->assertTrue($operator->match(null, ['test' => 'dDD']));
        $this->assertFalse($operator->match(null, ['test' => 'Ccc']));
    }
}
