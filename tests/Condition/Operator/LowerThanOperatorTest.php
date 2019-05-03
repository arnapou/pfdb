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

use Arnapou\PFDB\Condition\Operator\LowerThanOperator;
use Arnapou\PFDB\Tests\TestCase;

class LowerThanOperatorTest extends TestCase
{
    public function testInteger()
    {
        $operator = new LowerThanOperator('test', 100);

        $this->assertTrue($operator->match(null, ['test' => 99]));
        $this->assertFalse($operator->match(null, ['test' => 100]));
    }

    public function testFloat()
    {
        $operator = new LowerThanOperator('test', 1.25);

        $this->assertTrue($operator->match(null, ['test' => 1.24]));
        $this->assertFalse($operator->match(null, ['test' => 1.25]));
    }

    public function testString()
    {
        $operator = new LowerThanOperator('test', 'DDD');

        $this->assertFalse($operator->match(null, ['test' => 'ccc']));
        $this->assertTrue($operator->match(null, ['test' => 'DCC']));
        $this->assertFalse($operator->match(null, ['test' => 'EEE']));

        $operator = new LowerThanOperator('test', 'ddd', false);

        $this->assertTrue($operator->match(null, ['test' => 'CCC']));
        $this->assertTrue($operator->match(null, ['test' => 'dCC']));
        $this->assertFalse($operator->match(null, ['test' => 'Ecc']));
    }
}
