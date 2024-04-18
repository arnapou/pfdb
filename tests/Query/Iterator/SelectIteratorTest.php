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

namespace Arnapou\PFDB\Tests\Query\Iterator;

use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Iterator\SelectIterator;
use Arnapou\PFDB\Tests\Storage\PhpFileStorageTest;
use ArrayIterator;
use PHPUnit\Framework\TestCase;

class SelectIteratorTest extends TestCase
{
    public function testSelectAllFields(): void
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new ArrayIterator($data), ['*']);

        self::assertSame(
            ['id' => 1, 'name' => 'Red'],
            iterator_to_array($select)[0],
        );
    }

    public function testNoSelectFields(): void
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new ArrayIterator($data), []);

        self::assertSame(
            ['id' => 1, 'name' => 'Red'],
            iterator_to_array($select)[0],
        );
    }

    public function testBasicFields(): void
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new ArrayIterator($data), ['name']);

        self::assertSame(
            ['name' => 'Red'],
            iterator_to_array($select)[0],
        );
    }

    public function testSelectInterfaceFields(): void
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(new ArrayIterator($data), [new Field('id')]);

        self::assertSame(
            ['id' => 1],
            iterator_to_array($select)[0],
        );
    }

    public function testCallbackFields(): void
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('color');
        $select = new SelectIterator(
            new ArrayIterator($data),
            [
                function ($row, $key) {
                    return ['id:color' => $row['id'] . ':' . $row['name']];
                },
            ],
        );

        self::assertSame(
            ['id:color' => '1:Red'],
            iterator_to_array($select)[0],
        );
    }
}
