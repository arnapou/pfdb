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

use Arnapou\PFDB\Exception\LockException;
use Arnapou\PFDB\Lock\FileLocker;
use PHPUnit\Framework\TestCase;

class FileLockerTest extends TestCase
{
    public function testAcquireAndRelease(): void
    {
        $locker = new FileLocker();

        self::assertTrue($locker->acquire('foo'));
        self::assertFalse($locker->acquire('foo'));
        self::assertTrue($locker->acquire('bar'));
        self::assertTrue($locker->release('foo'));
        self::assertTrue($locker->acquire('foo'));
        self::assertFalse($locker->release('baz'));
    }

    public function testFailureWhenPathDoesNotExist(): void
    {
        $this->expectException(LockException::class);
        new FileLocker('/wtf/not/exist');
    }
}
