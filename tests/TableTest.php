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

use Arnapou\PFDB\Exception\PrimaryKeyAlreadyExistsException;
use Arnapou\PFDB\Exception\PrimaryKeyNotFoundException;
use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Exception\ValueNotFoundException;
use Arnapou\PFDB\Query\Helper\ExprTrait;
use Arnapou\PFDB\Storage\ArrayStorage;
use Arnapou\PFDB\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    use ExprTrait;

    public function testExceptionOnDelete()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->expectException(ValueNotFoundException::class);
        $table->delete('42');
    }

    public function testExceptionOnUpdate()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->expectException(ValueNotFoundException::class);
        $table->update(['id' => 42]);
    }

    public function testExceptionOnInsert()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $table->insert(['id' => 42]);
        $this->expectException(PrimaryKeyAlreadyExistsException::class);
        $table->insert(['id' => 42]);
    }

    public function testExceptionOnFlush()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $table->setReadonly(true);
        $table->insert(['id' => 42]);
        $this->expectException(ReadonlyException::class);
        $table->flush();
    }

    public function testExceptionOnLoad()
    {
        $this->expectException(PrimaryKeyNotFoundException::class);
        $table = new Table(new ArrayStorage(['test' => [['name' => 'Joe']]]), 'test', 'id');
    }

    public function testLoadWithNoPK()
    {
        $table = new Table(new ArrayStorage(), 'test', null);
        $this->assertCount(0, $table);
    }

    public function testFlushNoChange()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->assertFalse($table->flush());
    }

    public function testPrimaryKey()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->assertSame('id', $table->getPrimaryKey());
    }

    public function testGetData()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->assertSame([], $table->getData());
    }

    public function testInsert()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->insert(['name' => 'Joe']);
        $table->insert(['name' => 'Joe']);
        $this->assertSame(1, $table->getLastInsertedKey());

        $table->setPrimaryKeyGenerator(function () {
            return 42;
        });
        $table->insert(['name' => 'Joe']);
        $this->assertSame(42, $table->getLastInsertedKey());
    }

    public function testDelete()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->insert(['name' => 'Joe'], 42);
        $this->assertCount(1, $table);
        $table->delete(42);
        $this->assertCount(0, $table);
    }

    public function testUpdate()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->insert(['name' => 'Joe'], 42);
        $table->update(['name' => 'Lea'], 42);
        $this->assertSame('Lea', $table->get(42)['name']);
    }

    public function testUpsert()
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->upsert(['name' => 'Joe'], 42);
        $this->assertSame('Joe', $table->get(42)['name']);

        $table->upsert(['name' => 'Lea'], 42);
        $this->assertSame('Lea', $table->get(42)['name']);

        $table->upsert(['name' => 'Lea']);
        $this->assertSame(43, $table->getLastInsertedKey());
    }

    public function testUpdateMultiple()
    {
        $table = new Table(new ArrayStorage(), 'test', null);

        $table->upsert(['price' => 100]);
        $table->upsert(['price' => 150]);
        $table->upsert(['price' => 200]);
        $table->updateMultiple(
            $this->expr()->lte('price', 150),
            function ($row, $key) {
                $row['price'] /= 10;
                return $row;
            }
        );

        $this->assertSame([
            0 => ['price' => 10],
            1 => ['price' => 15],
            2 => ['price' => 200],
        ], $table->getData());
    }

    public function testDeleteMultiple()
    {
        $table = new Table(new ArrayStorage(), 'test', null);

        $table->upsert(['price' => 100]);
        $table->upsert(['price' => 150]);
        $table->upsert(['price' => 200]);
        $table->deleteMultiple(
            $this->expr()->lte('price', 150)
        );

        $this->assertSame([
            2 => ['price' => 200],
        ], $table->getData());
    }

    public function testClear()
    {
        $table = new Table(new ArrayStorage(), 'test', null);

        $table->upsert(['price' => 100]);
        $table->upsert(['price' => 150]);
        $table->upsert(['price' => 200]);
        $this->assertCount(3, $table);

        $table->clear();
        $this->assertCount(0, $table);
    }
}
