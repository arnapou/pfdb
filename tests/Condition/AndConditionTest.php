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

use Arnapou\PFDB\Condition\AndCondition;
use Arnapou\PFDB\Condition\Operator\GreaterThanOrEqualOperator;
use Arnapou\PFDB\Condition\Operator\LowerThanOperator;
use Arnapou\PFDB\Tests\TestCase;

class AndConditionTest extends TestCase
{
    public function testInteger()
    {
        $operator = new AndCondition([
            new GreaterThanOrEqualOperator('test', 100),
            new LowerThanOperator('test', 200),
        ]);

        $this->assertTrue($operator->match(null, ['test' => 100]));
        $this->assertTrue($operator->match(null, ['test' => 199]));
        $this->assertFalse($operator->match(null, ['test' => 99]));
        $this->assertFalse($operator->match(null, ['test' => 200]));
    }
}
