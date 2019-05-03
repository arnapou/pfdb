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

use Arnapou\PFDB\Condition\NotCondition;
use Arnapou\PFDB\Condition\Operator\EqualOperator;
use Arnapou\PFDB\Tests\TestCase;

class NotConditionTest extends TestCase
{
    public function testSimple()
    {
        $operator = new NotCondition(new EqualOperator('test', 'abc'));

        $this->assertFalse($operator->match(null, ['test' => 'abc']));
        $this->assertTrue($operator->match(null, ['test' => '123']));
    }
}
