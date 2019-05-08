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

use Arnapou\PFDB\ArrayTable;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Table;
use PHPUnit\Framework\TestCase;

class ArrayTableTest extends TestCase
{
    private const DATA = [
        ['id' => 1, 'name' => 'Red'],
        ['id' => 2, 'name' => 'Green'],
        ['id' => 3, 'name' => 'Blue'],
        ['id' => 4, 'name' => 'Yellow'],
        ['id' => 5, 'name' => 'Brown'],
    ];
    use ExprHelperTrait;

    public function test_getters()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $this->assertCount(5, $table);
        $this->assertSame('id', $table->getPrimaryKey());
        $this->assertSame(self::DATA, array_values($table->getData()));
        $this->assertSame(ArrayTable::NAME, $table->getName());
        $this->assertSame(['id' => 4, 'name' => 'Yellow'], $table->get(4));

        $table->setReadonly(true);
        $this->assertSame(true, $table->isReadonly());
        $this->assertInstanceOf(Table::class, $table->getIterator());
        
        $this->assertSame(false, $table->flush());
        $table->clear();
        $this->assertCount(0, $table);
    }

    public function test_delete()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->delete(2);
        $this->assertNull($table->get(2));
        $this->assertCount(4, $table);

        $table->deleteMultiple($this->expr()->gt('id', 2));
        $this->assertSame(['id' => 1, 'name' => 'Red'], $table->get(1));
        $this->assertCount(1, $table);
    }

    public function test_update()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->update(['id' => 3, 'name' => 'Orange']);
        $this->assertSame('Orange', $table->get(3)['name']);

        $table->updateMultiple($this->expr()->bool(true), function ($row, $key) {
            $row['upper'] = strtoupper($row['name']);
            return $row;
        });
        $this->assertSame(['RED', 'GREEN', 'ORANGE', 'YELLOW', 'BROWN'], array_column($table->getData(), 'upper'));
    }

    public function test_insert()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->insert(['id' => 6, 'name' => 'Orange']);
        $this->assertSame(['id' => 6, 'name' => 'Orange'], $table->get(6));

        $table->insert(['name' => 'Black']);
        $this->assertSame(7, $table->getLastInsertedKey());
        $this->assertSame('Black', $table->get(7)['name']);

        $table->insertMultiple([['id' => 60, 'name' => 'Purple'], ['name' => 'White']]);
        $this->assertSame(61, $table->getLastInsertedKey());
        $this->assertSame(['Purple', 'White'], array_column(iterator_to_array($table->find($this->expr()->gt('id', 20))), 'name'));
    }

    public function test_upsert()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->upsert(['id' => 2, 'name' => 'Purple']);
        $this->assertSame(['id' => 2, 'name' => 'Purple'], $table->get(2));

        $table->upsert(['name' => 'White']);
        $this->assertSame(['name' => 'White', 'id' => 6], $table->get(6));
    }
}
