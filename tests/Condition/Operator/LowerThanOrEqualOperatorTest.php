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

use Arnapou\PFDB\Condition\Operator\LowerThanOrEqualOperator;
use Arnapou\PFDB\Tests\TestCase;

class LowerThanOrEqualOperatorTest extends TestCase
{
    public function testInteger()
    {
        $operator = new LowerThanOrEqualOperator('test', 100);

        $this->assertTrue($operator->match(null, ['test' => 99]));
        $this->assertTrue($operator->match(null, ['test' => 100]));
        $this->assertFalse($operator->match(null, ['test' => 101]));
    }

    public function testFloat()
    {
        $operator = new LowerThanOrEqualOperator('test', 1.25);

        $this->assertTrue($operator->match(null, ['test' => 1.24]));
        $this->assertTrue($operator->match(null, ['test' => 1.25]));
        $this->assertFalse($operator->match(null, ['test' => 1.26]));
    }

    public function testString()
    {
        $operator = new LowerThanOrEqualOperator('test', 'DDD');

        $this->assertFalse($operator->match(null, ['test' => 'ccc']));
        $this->assertTrue($operator->match(null, ['test' => 'DCC']));
        $this->assertTrue($operator->match(null, ['test' => 'DDD']));
        $this->assertFalse($operator->match(null, ['test' => 'EEE']));

        $operator = new LowerThanOrEqualOperator('test', 'DDD', false);

        $this->assertTrue($operator->match(null, ['test' => 'ccc']));
        $this->assertTrue($operator->match(null, ['test' => 'dCc']));
        $this->assertTrue($operator->match(null, ['test' => 'DdD']));
        $this->assertFalse($operator->match(null, ['test' => 'Ecc']));
    }
}
