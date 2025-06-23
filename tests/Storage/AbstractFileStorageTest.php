<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Storage;

use Arnapou\PFDB\Exception\DirectoryNotFoundException;
use Arnapou\PFDB\Exception\InvalidTableNameException;
use Arnapou\PFDB\Storage\PhpFileStorage;
use Arnapou\PFDB\Tests\TestUtils;
use PHPUnit\Framework\TestCase;

class AbstractFileStorageTest extends TestCase
{
    public function testDirectoryNotExists(): void
    {
        $this->expectException(DirectoryNotFoundException::class);
        new PhpFileStorage('/does/not/exists/or/very/not/lucky');
    }

    public function testInvalidTablename(): void
    {
        $this->expectException(InvalidTableNameException::class);
        PhpFileStorageTest::pfdbStorage()->load('bad:characters@');
    }

    public function testInvalidPrefixTablename(): void
    {
        $this->expectException(InvalidTableNameException::class);
        new PhpFileStorage(sys_get_temp_dir(), 'bad:characters@');
    }

    public function testGetPath(): void
    {
        $storage = new PhpFileStorage(__DIR__ . DIRECTORY_SEPARATOR);

        self::assertSame(__DIR__, $storage->getPath());
    }

    public function testTablenames(): void
    {
        self::assertSame(['vehicle'], YamlFileStorageTest::pfdbStorage()->tableNames());
    }

    public function testReadonlyFolder(): void
    {
        $dir = sys_get_temp_dir() . '/test_' . md5(uniqid('', true) . mt_rand(0, PHP_INT_MAX));
        if (!is_dir($dir)) {
            if ($this->mkdirFailed($dir)) {
                self::markTestSkipped('chmod does not work');
            }
            $storage = new PhpFileStorage($dir);
            self::assertTrue($storage->isReadonly('any_folder'));
            @rmdir($dir);
        } else {
            self::markTestSkipped('test folder already exists. That should never occur.');
        }
    }

    private function mkdirFailed(string $dir): bool
    {
        return TestUtils::inGitlabCI() || !mkdir($dir, 0, true);
    }
}
