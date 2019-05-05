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
    public function testDirectoryNotExists()
    {
        $this->expectException(DirectoryNotFoundException::class);
        new PhpFileStorage('/does/not/exists/or/very/not/lucky');
    }

    public function testInvalidTableName()
    {
        $this->expectException(InvalidTableNameException::class);
        PhpFileStorageTest::pfdbStorage()->load('bad:characters@');
    }

    public function testInvalidPrefixTableName()
    {
        $this->expectException(InvalidTableNameException::class);
        new PhpFileStorage(sys_get_temp_dir(), 'bad:characters@');
    }

    public function testGetPath()
    {
        $storage = new PhpFileStorage(__DIR__ . DIRECTORY_SEPARATOR);

        $this->assertSame(__DIR__, $storage->getPath());
    }

    public function testTableNames()
    {
        $this->assertSame(['vehicle'], YamlFileStorageTest::pfdbStorage()->tableNames());
    }

    public function testReadonlyFolder()
    {
        $dir = sys_get_temp_dir() . '/test_' . md5(uniqid('', true) . mt_rand(0, PHP_INT_MAX));
        if (!is_dir($dir)) {
            mkdir($dir, 0000, true);
            $storage = new PhpFileStorage($dir);
            $this->assertTrue($storage->isReadonly('any_folder'));
            @rmdir($dir);
        } else {
            $this->markTestSkipped('test folder already exists. That should never occur.');
        }
    }
}
