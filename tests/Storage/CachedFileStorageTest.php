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

use Arnapou\PFDB\Storage\CachedFileStorage;
use Arnapou\PFDB\Storage\PhpFileStorage;
use Arnapou\PFDB\Storage\YamlFileStorage;
use Arnapou\PFDB\Tests\ArrayTableTest;
use PHPUnit\Framework\TestCase;

class CachedFileStorageTest extends TestCase
{
    const TMP_NAME = 'test_table_cached';

    private function fileStorage(): CachedFileStorage
    {
        $storage = new CachedFileStorage(new YamlFileStorage(sys_get_temp_dir()), sys_get_temp_dir());
        if (is_file($storage->innerStorage()->getFilename(self::TMP_NAME))) {
            @unlink($storage->innerStorage()->getFilename(self::TMP_NAME));
        }
        if (is_file($storage->cacheStorage()->getFilename(self::TMP_NAME))) {
            @unlink($storage->cacheStorage()->getFilename(self::TMP_NAME));
        }
        return $storage;
    }

    public function test_getters()
    {
        $storage = $this->fileStorage();

        self::assertFalse($storage->isReadonly(self::TMP_NAME));
        self::assertInstanceOf(PhpFileStorage::class, $storage->cacheStorage());
        self::assertInstanceOf(YamlFileStorage::class, $storage->innerStorage());
        self::assertIsArray($storage->tableNames());
    }

    public function test_save_and_delete()
    {
        $storage = $this->fileStorage();
        $storage->save(self::TMP_NAME, ArrayTableTest::DATA);

        self::assertCount(5, $storage->load(self::TMP_NAME));
        self::assertSame(
            $storage->cacheStorage()->load(self::TMP_NAME),
            $storage->innerStorage()->load(self::TMP_NAME)
        );

        $storage->delete(self::TMP_NAME);
        self::assertFalse(is_file($storage->cacheStorage()->getFilename(self::TMP_NAME)));
        self::assertFalse(is_file($storage->innerStorage()->getFilename(self::TMP_NAME)));
    }

    public function test_source_not_exists_with_remaining_cache_return_empty()
    {
        $storage = $this->fileStorage();
        $storage->cacheStorage()->save(self::TMP_NAME, ArrayTableTest::DATA);
        self::assertCount(5, $storage->cacheStorage()->load(self::TMP_NAME));
        self::assertCount(0, $storage->load(self::TMP_NAME));
    }

    public function test_source_exists_with_cache_not_present()
    {
        $storage = $this->fileStorage();
        $storage->innerStorage()->save(self::TMP_NAME, ArrayTableTest::DATA);
        self::assertCount(5, $storage->load(self::TMP_NAME));
    }

    public function test_cache_loaded_instead_of_source()
    {
        $storage = $this->fileStorage();
        $DATA    = ArrayTableTest::DATA;
        $storage->save(self::TMP_NAME, $DATA);
        self::assertCount(5, $storage->load(self::TMP_NAME));

        // hack cached storage with fresher timestamp ... bad idea in real use case ... here just for testing !
        touch($storage->innerStorage()->getFilename(self::TMP_NAME), time() - 10);
        $DATA[0]['name'] = 'XXX';
        $storage->cacheStorage()->save(self::TMP_NAME, $DATA);
        self::assertSame($DATA, $storage->load(self::TMP_NAME));
    }
}
