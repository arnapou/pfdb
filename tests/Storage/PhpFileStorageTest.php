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

    public function testCount()
    {
        $storage = self::pfdbStorage();

        $this->assertSame(9, \count($storage->load('vehicle')));
        $this->assertSame(0, \count($storage->load('not_exists')));
    }

    public function testSave()
    {
        $storage = $this->fileStorage(false);
        $storage->save(self::TMP_NAME, []);
        $this->assertTrue(true);
    }

    public function testSaveReadonly()
    {
        $storage = $this->fileStorage(true);
        $this->expectException(ReadonlyException::class);
        $storage->save(self::TMP_NAME, []);
    }

    public function testDelete()
    {
        $storage = $this->fileStorage(false);
        $storage->delete(self::TMP_NAME);
        $this->assertTrue(true);
    }

    public function testDeleteReadonly()
    {
        $storage = $this->fileStorage(true);
        $this->expectException(ReadonlyException::class);
        $storage->delete(self::TMP_NAME);
    }
}
