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

use Arnapou\PFDB\Condition\Operator\InOperator;
use Arnapou\PFDB\Tests\TestCase;

class InOperatorTest extends TestCase
{
    public function testInteger()
    {
        $operator = new InOperator('test', [4,5,6]);

        $this->assertTrue($operator->match(null, ['test' => 4]));
        $this->assertTrue($operator->match(null, ['test' => '4']));
        $this->assertFalse($operator->match(null, ['test' => 7]));
    }

    public function testFloat()
    {
        $operator = new InOperator('test', [4.0, '4.1', 4.2]);

        $this->assertTrue($operator->match(null, ['test' => 4.1]));
        $this->assertTrue($operator->match(null, ['test' => 4.0]));
        $this->assertFalse($operator->match(null, ['test' => '4.0']));
        $this->assertFalse($operator->match(null, ['test' => 4.5]));
    }

    public function testString()
    {
        $operator = new InOperator('test', ['a', 'b', '6']);

        $this->assertTrue($operator->match(null, ['test' => 6]));
        $this->assertTrue($operator->match(null, ['test' => 'a']));
        $this->assertFalse($operator->match(null, ['test' => 'c']));

        $operator = new InOperator('test', ['a', 'b', '6'], false);

        $this->assertTrue($operator->match(null, ['test' => 'A']));
    }
}
