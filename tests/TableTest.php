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

namespace Arnapou\PFDB\Tests;

use Arnapou\PFDB\Exception\PrimaryKeyAlreadyExistsException;
use Arnapou\PFDB\Exception\PrimaryKeyNotFoundException;
use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Exception\ValueNotFoundException;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Storage\ArrayStorage;
use Arnapou\PFDB\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    use ExprHelperTrait;

    public function testExceptionOnDelete(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->expectException(ValueNotFoundException::class);
        $table->delete('42');
    }

    public function testExceptionOnUpdate(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $this->expectException(ValueNotFoundException::class);
        $table->update(['id' => 42]);
    }

    public function testExceptionOnInsert(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $table->insert(['id' => 42]);
        $this->expectException(PrimaryKeyAlreadyExistsException::class);
        $table->insert(['id' => 42]);
    }

    public function testExceptionOnFlush(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        $table->setReadonly(true);
        $table->insert(['id' => 42]);
        $this->expectException(ReadonlyException::class);
        $table->flush();
    }

    public function testExceptionOnLoad(): void
    {
        $this->expectException(PrimaryKeyNotFoundException::class);
        $table = new Table(new ArrayStorage(['test' => [['name' => 'Joe']]]), 'test', 'id');
    }

    public function testLoadWithNoPk(): void
    {
        $table = new Table(new ArrayStorage(), 'test', null);
        self::assertCount(0, $table);
    }

    public function testFlushNoChange(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        self::assertFalse($table->flush());
    }

    public function testPrimaryKey(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        self::assertSame('id', $table->getPrimaryKey());
    }

    public function testGetData(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');
        self::assertSame([], $table->getData());
    }

    public function testInsert(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->insert(['name' => 'Joe']);
        $table->insert(['name' => 'Joe']);
        self::assertSame(1, $table->getLastInsertedKey());

        $table->setPrimaryKeyGenerator(
            function () {
                return 42;
            },
        );
        $table->insert(['name' => 'Joe']);
        self::assertSame(42, $table->getLastInsertedKey());
    }

    public function testDelete(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->insert(['name' => 'Joe'], 42);
        self::assertCount(1, $table);
        $table->delete(42);
        self::assertCount(0, $table);
    }

    public function testUpdate(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->insert(['name' => 'Joe'], 42);
        $table->update(['name' => 'Lea'], 42);
        self::assertSame('Lea', $table->get(42)['name'] ?? null);
    }

    public function testUpsert(): void
    {
        $table = new Table(new ArrayStorage(), 'test', 'id');

        $table->upsert(['name' => 'Joe'], 42);
        self::assertSame('Joe', $table->get(42)['name'] ?? null);

        $table->upsert(['name' => 'Lea'], 42);
        self::assertSame('Lea', $table->get(42)['name']);

        $table->upsert(['name' => 'Lea']);
        self::assertSame(43, $table->getLastInsertedKey());
    }

    public function testUpdateMultiple(): void
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
            },
        );

        self::assertSame(
            [
                0 => ['price' => 10],
                1 => ['price' => 15],
                2 => ['price' => 200],
            ],
            $table->getData(),
        );
    }

    public function testDeleteMultiple(): void
    {
        $table = new Table(new ArrayStorage(), 'test', null);

        $table->upsert(['price' => 100]);
        $table->upsert(['price' => 150]);
        $table->upsert(['price' => 200]);
        $table->deleteMultiple(
            $this->expr()->lte('price', 150),
        );

        self::assertSame(
            [
                2 => ['price' => 200],
            ],
            $table->getData(),
        );
    }

    public function testClear(): void
    {
        $table = new Table(new ArrayStorage(), 'test', null);

        $table->upsert(['price' => 100]);
        $table->upsert(['price' => 150]);
        $table->upsert(['price' => 200]);
        self::assertCount(3, $table);

        $table->clear();
        self::assertCount(0, $table);
    }
}
