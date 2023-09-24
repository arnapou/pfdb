<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Lock;

use Arnapou\PFDB\Lock\RedisLocker;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisLockerTest extends TestCase
{
    public function testAcquireAndRelease(): void
    {
        $locker = new RedisLocker($mock = $this->createMock(Redis::class));

        $locks = [];

        $mock->expects($this->atLeastOnce())->method('set')->willReturnCallback(
            function (string $key) use (&$locks) {
                $ok = empty($locks[$key]);
                $locks[$key] = true;

                return $ok;
            }
        );
        $mock->expects($this->atLeastOnce())->method('del')->willReturnCallback(
            function (string $key) use (&$locks) {
                unset($locks[$key]);

                return false;
            }
        );

        self::assertTrue($locker->acquire('foo'));
        self::assertFalse($locker->acquire('foo'));
        self::assertTrue($locker->acquire('bar'));
        self::assertTrue($locker->release('foo'));
        self::assertTrue($locker->acquire('foo'));
    }
}
