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
    public function testSelectAllFields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), ['*']);

        $this->assertSame(
            ['id' => 1, 'name' => 'Red'],
            iterator_to_array($select)[0]
        );
    }

    public function testNoSelectFields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), []);

        $this->assertSame(
            ['id' => 1, 'name' => 'Red'],
            iterator_to_array($select)[0]
        );
    }

    public function testBasicFields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), ['name']);

        $this->assertSame(
            ['name' => 'Red'],
            iterator_to_array($select)[0]
        );
    }

    public function testSelectInterfaceFields()
    {
        $data   = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new \ArrayIterator($data), [new Field('id')]);

        $this->assertSame(
            ['id' => 1],
            iterator_to_array($select)[0]
        );
    }

    public function testCallbackFields()
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
