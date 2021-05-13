<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests;

use Arnapou\PFDB\Database;
use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Storage\ReadonlyStorage;
use Arnapou\PFDB\Tests\Storage\PhpFileStorageTest;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public static function pfdbDatabase(): Database
    {
        $db = new Database(
            new ReadonlyStorage(
                PhpFileStorageTest::pfdbStorage()
            )
        );
        return $db;
    }

    public function test_tables()
    {
        $db = self::pfdbDatabase();

        self::assertCount(4, $db->getTables());
    }

    public function test_drop_exception()
    {
        $db = self::pfdbDatabase();
        $table =$db->getTable('vehicle');

        $db->dropTable($table);

        $this->expectException(ReadonlyException::class);
        $table->setReadonly(true);

        $db->dropTable($table);
    }
}
