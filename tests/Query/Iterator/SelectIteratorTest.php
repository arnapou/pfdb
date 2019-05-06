<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query\Iterator;

use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Iterator\SelectIterator;
use Arnapou\PFDB\Tests\Storage\PhpFileStorageTest;
use PHPUnit\Framework\TestCase;

class SelectIteratorTest extends TestCase
{
    public function test_select_all_fields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), ['*']);

        $this->assertSame(
            ['id' => 1, 'name' => 'Red'],
            iterator_to_array($select)[0]
        );
    }

    public function test_no_select_fields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), []);

        $this->assertSame(
            ['id' => 1, 'name' => 'Red'],
            iterator_to_array($select)[0]
        );
    }

    public function test_basic_fields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), ['name']);

        $this->assertSame(
            ['name' => 'Red'],
            iterator_to_array($select)[0]
        );
    }

    public function test_select_interface_fields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), [new Field('id')]);

        $this->assertSame(
            ['id' => 1],
            iterator_to_array($select)[0]
        );
    }

    public function test_callback_fields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), [
            function ($row, $key) {
                return ['id:color' => $row['id'] . ':' . $row['name']];
            },
        ]);

        $this->assertSame(
            ['id:color' => '1:Red'],
            iterator_to_array($select)[0]
        );
    }
}
