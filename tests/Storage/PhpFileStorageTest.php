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
use Arnapou\PFDB\Storage\PhpFileStorage;

use function count;

use PHPUnit\Framework\TestCase;

class PhpFileStorageTest extends TestCase
{
    public const TMP_NAME = 'test_table';

    public static function pfdbStorage(): PhpFileStorage
    {
        return new PhpFileStorage(__DIR__ . '/../../demo/database');
    }

    private function chmodFailed(string $filename): bool
    {
        return getenv('CI_JOB_NAME') || !chmod($filename, 0);
    }

    private function fileStorage(bool $readonly): PhpFileStorage
    {
        $storage = new PhpFileStorage(sys_get_temp_dir());
        if (is_file($storage->getFilename(self::TMP_NAME))) {
            @unlink($storage->getFilename(self::TMP_NAME));
        }
        $storage->save(self::TMP_NAME, []);
        if ($readonly && $this->chmodFailed($storage->getFilename(self::TMP_NAME))) {
            self::markTestSkipped('chmod does not work');
        }

        return $storage;
    }

    public function testCount()
    {
        $storage = self::pfdbStorage();

        self::assertSame(9, count($storage->load('vehicle')));
        self::assertSame(0, count($storage->load('not_exists')));
    }

    public function testSave()
    {
        $storage = $this->fileStorage(false);
        $storage->save(self::TMP_NAME, []);
        self::assertTrue(true);
    }

    public function testSaveReadonlyRaisesException()
    {
        $storage = $this->fileStorage(true);
        $this->expectException(ReadonlyException::class);
        $storage->save(self::TMP_NAME, []);
    }

    public function testDelete()
    {
        $storage = $this->fileStorage(false);
        $storage->delete(self::TMP_NAME);
        self::assertTrue(true);
    }

    public function testDeleteReadonlyRaisesException()
    {
        $storage = $this->fileStorage(true);
        $this->expectException(ReadonlyException::class);
        $storage->delete(self::TMP_NAME);
    }
}
