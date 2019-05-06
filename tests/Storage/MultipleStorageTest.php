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

use Arnapou\PFDB\Exception\StorageException;
use Arnapou\PFDB\Storage\ArrayStorage;
use Arnapou\PFDB\Storage\MultipleStorage;
use PHPUnit\Framework\TestCase;

class MultipleStorageTest extends TestCase
{
    public function test_misc()
    {
        $child1  = new ArrayStorage(['table1' => [['data1' => 1]]]);
        $child2  = new ArrayStorage(['table2' => [['data2' => 2]]]);
        $storage = new MultipleStorage($child1);
        $storage->add($child2);

        $this->assertSame(['table1', 'table2'], $storage->tableNames());
        $this->assertCount(2, $storage->children());
        $this->assertSame($child2, $storage->findChild('table2'));
        $this->assertNull($storage->findChild('not_exists'));
        $this->assertSame([['data1' => 1]], $storage->load('table1'));
        $this->assertFalse($storage->isReadonly('any_table'));

        // copy data 2 -> 1
        $storage->save('table1', $storage->load('table2'));
        $this->assertSame([['data2' => 2]], $storage->load('table1'));

        // create table (default in data 1)
        $storage->save('table3', $storage->load('table2'));
        $this->assertSame([['data2' => 2]], $storage->load('table3'));

        // delete table
        $storage->delete('table3');
        $this->assertNull($storage->findChild('table3'));
    }

    public function test_exception_on_save()
    {
        $storage = new MultipleStorage();
        $this->expectException(StorageException::class);
        $storage->save('we_dont_care', []);
    }

    public function test_exception_on_delete()
    {
        $storage = new MultipleStorage();
        $this->expectException(StorageException::class);
        $storage->delete('we_dont_care');
    }
}
