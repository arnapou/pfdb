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

namespace Arnapou\PFDB\Tests\Lock\Decorator;

use Arnapou\PFDB\Lock\Decorator\WaitingLocker;
use Arnapou\PFDB\Lock\Locker;
use PHPUnit\Framework\TestCase;

class WaitingLockerTest extends TestCase
{
    public function testRelease(): void
    {
        $locker = new WaitingLocker(
            $mock = $this->createMock(Locker::class),
            maxTotalWaitSeconds: 1
        );

        $mock->expects($this->once())->method('release')->willReturn(true);
        self::assertTrue($locker->release('name'));
    }

    public function testAcquireWaitMax(): void
    {
        $locker = new WaitingLocker(
            $mock = $this->createMock(Locker::class),
            maxTotalWaitSeconds: $maxWait = 0.300
        );

        $start = microtime(true);
        $mock->expects($this->exactly(6))->method('acquire')->willReturn(false);
        self::assertFalse($locker->acquire('name'));
        self::assertTrue(microtime(true) - $start >= $maxWait);
    }
}
