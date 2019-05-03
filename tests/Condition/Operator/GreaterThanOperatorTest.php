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

use Arnapou\PFDB\Condition\Operator\GreaterThanOperator;
use Arnapou\PFDB\Tests\TestCase;

class GreaterThanOperatorTest extends TestCase
{
    public function testInteger()
    {
        $operator = new GreaterThanOperator('test', 100);

        $this->assertTrue($operator->match(null, ['test' => 101]));
        $this->assertFalse($operator->match(null, ['test' => 100]));
    }

    public function testFloat()
    {
        $operator = new GreaterThanOperator('test', 1.25);

        $this->assertTrue($operator->match(null, ['test' => 1.26]));
        $this->assertFalse($operator->match(null, ['test' => 1.25]));
    }

    public function testString()
    {
        $operator = new GreaterThanOperator('test', 'ddd');

        $this->assertFalse($operator->match(null, ['test' => 'EEE']));
        $this->assertTrue($operator->match(null, ['test' => 'dee']));
        $this->assertFalse($operator->match(null, ['test' => 'ccc']));

        $operator = new GreaterThanOperator('test', 'ddd', false);

        $this->assertTrue($operator->match(null, ['test' => 'EEE']));
        $this->assertTrue($operator->match(null, ['test' => 'dEE']));
        $this->assertFalse($operator->match(null, ['test' => 'Ccc']));
    }
}
