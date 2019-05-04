<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query;

use Arnapou\PFDB\Query\Expr\ExprTrait;
use Arnapou\PFDB\Tests\TestCase;

class NestedExprTest extends TestCase
{
    use ExprTrait;

    public function testAND()
    {
        $expr = $this->expr()->and(
            $this->expr()->eq('name', 'Joe'),
            $this->expr()->gt('age', 20)
        );

        $this->assertTrue(\call_user_func($expr, ['name' => 'Joe', 'age' => 22]));
        $this->assertFalse(\call_user_func($expr, ['name' => 'Joe', 'age' => 20]));
        $this->assertFalse(\call_user_func($expr, ['name' => 'Helen', 'age' => 22]));
    }

    public function testOR()
    {
        $expr = $this->expr()->or(
            $this->expr()->eq('name', 'Joe'),
            $this->expr()->gt('age', 20)
        );

        $this->assertTrue(\call_user_func($expr, ['name' => 'Joe', 'age' => 22]));
        $this->assertTrue(\call_user_func($expr, ['name' => 'Joe', 'age' => 20]));
        $this->assertTrue(\call_user_func($expr, ['name' => 'Helen', 'age' => 22]));
        $this->assertFalse(\call_user_func($expr, ['name' => 'Helen', 'age' => 20]));
    }

    public function testNOT()
    {
        $expr = $this->expr()->not(
            $this->expr()->or(
                $this->expr()->eq('name', 'Joe'),
                $this->expr()->gt('age', 20)
            )
        );

        $this->assertFalse(\call_user_func($expr, ['name' => 'Joe', 'age' => 22]));
        $this->assertFalse(\call_user_func($expr, ['name' => 'Joe', 'age' => 20]));
        $this->assertFalse(\call_user_func($expr, ['name' => 'Helen', 'age' => 22]));
        $this->assertTrue(\call_user_func($expr, ['name' => 'Helen', 'age' => 20]));
    }
}
