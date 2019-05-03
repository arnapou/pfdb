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

use Arnapou\PFDB\Condition\Operator\RegExpOperator;
use Arnapou\PFDB\Tests\TestCase;

class RegExpOperatorTest extends TestCase
{
    public function testSimple()
    {
        $operator = new RegExpOperator('test', 'oo');

        $this->assertTrue($operator->match(null, ['test' => 'foo']));
        $this->assertFalse($operator->match(null, ['test' => 'bar']));
    }

    public function testString()
    {
        $operator = new RegExpOperator('test', '^f?oo$');

        $this->assertFalse($operator->match(null, ['test' => 'foo bar']));
        $this->assertTrue($operator->match(null, ['test' => 'foo']));
        $this->assertTrue($operator->match(null, ['test' => 'oo']));

        $operator = new RegExpOperator('test', '^f?oo$', false);

        $this->assertFalse($operator->match(null, ['test' => 'foo bar']));
        $this->assertTrue($operator->match(null, ['test' => 'fOO']));
        $this->assertTrue($operator->match(null, ['test' => 'Oo']));
    }
}
