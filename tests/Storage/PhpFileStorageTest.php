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

use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Storage\PhpFileStorage;
use PHPUnit\Framework\TestCase;

class PhpFileStorageTest extends TestCase
{
    const TMP_NAME = 'test_table';

    public static function pfdbStorage(): PhpFileStorage
    {
        return new PhpFileStorage(__DIR__ . '/../../demo/database');
    }

    private function fileStorage(bool $readonly): PhpFileStorage
    {
        $storage = new PhpFileStorage(sys_get_temp_dir());
        if (is_file($storage->getFilename(self::TMP_NAME))) {
            @unlink($storage->getFilename(self::TMP_NAME));
        }
        $storage->save(self::TMP_NAME, []);
        if ($readonly) {
            chmod($storage->getFilename(self::TMP_NAME), 000);
        }
        return $storage;
    }

    public function test_count()
    {
        $storage = self::pfdbStorage();

        self::assertSame(9, \count($storage->load('vehicle')));
        self::assertSame(0, \count($storage->load('not_exists')));
    }

    public function test_save()
    {
        $storage = $this->fileStorage(false);
        $storage->save(self::TMP_NAME, []);
        self::assertTrue(true);
    }

    public function test_save_readonly_raises_exception()
    {
        $storage = $this->fileStorage(true);
        $this->expectException(ReadonlyException::class);
        $storage->save(self::TMP_NAME, []);
    }

    public function test_delete()
    {
        $storage = $this->fileStorage(false);
        $storage->delete(self::TMP_NAME);
        self::assertTrue(true);
    }

    public function test_delete_readonly_raises_exception()
    {
        $storage = $this->fileStorage(true);
        $this->expectException(ReadonlyException::class);
        $storage->delete(self::TMP_NAME);
    }
}
