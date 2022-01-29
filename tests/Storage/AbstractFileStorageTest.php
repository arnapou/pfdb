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

use Arnapou\PFDB\Exception\DirectoryNotFoundException;
use Arnapou\PFDB\Exception\InvalidTableNameException;
use Arnapou\PFDB\Storage\PhpFileStorage;
use PHPUnit\Framework\TestCase;

class AbstractFileStorageTest extends TestCase
{
    public function test_directory_not_exists()
    {
        $this->expectException(DirectoryNotFoundException::class);
        new PhpFileStorage('/does/not/exists/or/very/not/lucky');
    }

    public function test_invalid_tablename()
    {
        $this->expectException(InvalidTableNameException::class);
        PhpFileStorageTest::pfdbStorage()->load('bad:characters@');
    }

    public function test_invalid_prefix_tablename()
    {
        $this->expectException(InvalidTableNameException::class);
        new PhpFileStorage(sys_get_temp_dir(), 'bad:characters@');
    }

    public function test_get_path()
    {
        $storage = new PhpFileStorage(__DIR__ . DIRECTORY_SEPARATOR);

        self::assertSame(__DIR__, $storage->getPath());
    }

    public function test_tablenames()
    {
        self::assertSame(['vehicle'], YamlFileStorageTest::pfdbStorage()->tableNames());
    }

    public function test_readonly_folder()
    {
        $dir = sys_get_temp_dir() . '/test_' . md5(uniqid('', true) . mt_rand(0, PHP_INT_MAX));
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0000, true)) {
                self::markTestSkipped('chmod does not work');
            }
            $storage = new PhpFileStorage($dir);
            self::assertTrue($storage->isReadonly('any_folder'));
            @rmdir($dir);
        } else {
            self::markTestSkipped('test folder already exists. That should never occur.');
        }
    }
}
