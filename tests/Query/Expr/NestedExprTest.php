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

use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use PHPUnit\Framework\TestCase;

class NestedExprTest extends TestCase
{
    use ExprHelperTrait;

    public function test_AND()
    {
        $expr = $this->expr()->and(
            $this->expr()->eq('name', 'Joe'),
            $this->expr()->gt('age', 20)
        );

        self::assertTrue(\call_user_func($expr, ['name' => 'Joe', 'age' => 22]));
        self::assertFalse(\call_user_func($expr, ['name' => 'Joe', 'age' => 20]));
        self::assertFalse(\call_user_func($expr, ['name' => 'Helen', 'age' => 22]));
    }

    public function test_OR()
    {
        $expr = $this->expr()->or(
            $this->expr()->eq('name', 'Joe'),
            $this->expr()->gt('age', 20)
        );

        self::assertTrue(\call_user_func($expr, ['name' => 'Joe', 'age' => 22]));
        self::assertTrue(\call_user_func($expr, ['name' => 'Joe', 'age' => 20]));
        self::assertTrue(\call_user_func($expr, ['name' => 'Helen', 'age' => 22]));
        self::assertFalse(\call_user_func($expr, ['name' => 'Helen', 'age' => 20]));
    }

    public function test_NOT()
    {
        $expr = $this->expr()->not(
            $this->expr()->or(
                $this->expr()->eq('name', 'Joe'),
                $this->expr()->gt('age', 20)
            )
        );

        self::assertFalse(\call_user_func($expr, ['name' => 'Joe', 'age' => 22]));
        self::assertFalse(\call_user_func($expr, ['name' => 'Joe', 'age' => 20]));
        self::assertFalse(\call_user_func($expr, ['name' => 'Helen', 'age' => 22]));
        self::assertTrue(\call_user_func($expr, ['name' => 'Helen', 'age' => 20]));
    }

    public function test_children_count()
    {
        self::assertCount(
            3,
            $this->expr()->and(
                $this->expr()->eq('field1', 1),
                $this->expr()->eq('field2', 2),
                $this->expr()->eq('field3', 3)
            )->children()
        );
    }
}
