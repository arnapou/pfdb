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

namespace Arnapou\PFDB\Tests\Storage;

use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Storage\ArrayStorage;
use Arnapou\PFDB\Storage\ReadonlyStorage;
use Arnapou\PFDB\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class ReadonlyStorageTest extends TestCase
{
    public static function fakeStorage(): StorageInterface
    {
        return new class() implements StorageInterface {
            public function load(string $name): array
            {
                return [['name' => 'Joe']];
            }

            public function save(string $name, array $data): void
            {
                // nothing
            }

            public function isReadonly(string $name): bool
            {
                return true;
            }

            public function delete(string $name): void
            {
                // nothing
            }

            public function tableNames(): array
            {
                return ['any'];
            }
        };
    }

    public function testExceptionOnSave()
    {
        $storage = new ReadonlyStorage(self::fakeStorage(), false);
        $this->expectException(ReadonlyException::class);
        $storage->save('we_dont_care', []);
    }

    public function testExceptionOnDelete()
    {
        $storage = new ReadonlyStorage(self::fakeStorage(), false);
        $this->expectException(ReadonlyException::class);
        $storage->delete('we_dont_care');
    }

    public function testForCoverage()
    {
        $storage = new ReadonlyStorage(self::fakeStorage(), false);

        self::assertSame(['any'], $storage->tableNames());
        self::assertTrue($storage->isReadonly('we_dont_care'));
        self::assertInstanceOf(StorageInterface::class, $storage->innerStorage());

        $storage = new ReadonlyStorage(new ArrayStorage());
        self::assertFalse($storage->isReadonly('we_dont_care'));
        $storage->save('we_dont_care', []);
        $storage->delete('we_dont_care');
    }
}
