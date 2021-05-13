<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Storage;

use Arnapou\PFDB\Storage\ArrayStorage;
use Arnapou\PFDB\Storage\LockedStorage;
use PHPUnit\Framework\TestCase;

class LockedStorageTest extends TestCase
{
    public function test_misc()
    {
        $storage = new LockedStorage(new ArrayStorage(['tata' => []]));

        self::assertSame([], $storage->load('tutu'));
        self::assertSame(['tata'], $storage->tableNames());
        self::assertInstanceOf(ArrayStorage::class, $storage->innerStorage());
        self::assertFalse($storage->isReadonly('tata'));

        $storage->delete('tutu');
        $storage->save('tutu', []);
    }
}
