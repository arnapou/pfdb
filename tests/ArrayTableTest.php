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

use Arnapou\PFDB\ArrayTable;
use Arnapou\PFDB\Exception\MultipleActionException;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Table;
use PHPUnit\Framework\TestCase;

class ArrayTableTest extends TestCase
{
    const DATA = [
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

        self::assertCount(5, $table);
        self::assertSame('id', $table->getPrimaryKey());
        self::assertSame(self::DATA, array_values($table->getData()));
        self::assertSame(ArrayTable::NAME, $table->getName());
        self::assertSame(['id' => 4, 'name' => 'Yellow'], $table->get(4));

        $table->setReadonly(true);
        self::assertSame(true, $table->isReadonly());
        self::assertInstanceOf(Table::class, $table->getIterator());

        self::assertSame(false, $table->flush());
        $table->clear();
        self::assertCount(0, $table);
    }

    public function test_delete()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->delete(2);
        self::assertNull($table->get(2));
        self::assertCount(4, $table);

        $table->deleteMultiple($this->expr()->gt('id', 2));
        self::assertSame(['id' => 1, 'name' => 'Red'], $table->get(1));
        self::assertCount(1, $table);
    }

    public function test_update()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->update(['id' => 3, 'name' => 'Orange']);
        self::assertSame('Orange', $table->get(3)['name']);

        $table->updateMultiple(
            $this->expr()->bool(true),
            function ($row, $key) {
                $row['upper'] = strtoupper($row['name']);
                return $row;
            }
        );
        self::assertSame(['RED', 'GREEN', 'ORANGE', 'YELLOW', 'BROWN'], array_column($table->getData(), 'upper'));
    }

    public function test_insert()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->insert(['id' => 6, 'name' => 'Orange']);
        self::assertSame(['id' => 6, 'name' => 'Orange'], $table->get(6));

        $table->insert(['name' => 'Black']);
        self::assertSame(7, $table->getLastInsertedKey());
        self::assertSame('Black', $table->get(7)['name']);

        $table->insertMultiple([['id' => 60, 'name' => 'Purple'], ['name' => 'White']]);
        self::assertSame(61, $table->getLastInsertedKey());
        self::assertSame(['Purple', 'White'], array_column(iterator_to_array($table->find($this->expr()->gt('id', 20))), 'name'));
    }

    public function test_insert_multiple_with_exception_should_not_change_data()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $keys = array_keys($table->getData());
        try {
            $table->insertMultiple([['name' => 'White'], ['id' => 6, 'name' => 'Purple']]);
            $this->fail('a MultipleActionException should have been raised for the multiple insert');
        } catch (MultipleActionException $exception) {
            self::assertSame($keys, array_keys($table->getData()));
        }
    }

    public function test_update_multiple_with_exception_should_not_change_data()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $keys = array_keys($table->getData());
        try {
            $table->updateMultiple(
                $this->expr()->bool(true),
                function ($row, $key) {
                    $row['name'] .= $row['not_existsing_field_will_cause_error'];
                    return $row;
                }
            );
            $this->fail('a MultipleActionException should have been raised for the multiple update');
        } catch (MultipleActionException $exception) {
            self::assertSame($keys, array_keys($table->getData()));
        }
    }

    public function test_delete_multiple_with_exception_should_not_change_data()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $keys = array_keys($table->getData());
        try {
            $table->deleteMultiple(
                $this->expr()->func(
                    function ($row, $key) {
                        return $row['not_existsing_field_will_cause_error'] == 42;
                    }
                )
            );
            $this->fail('a MultipleActionException should have been raised for the multiple delete');
        } catch (MultipleActionException $exception) {
            self::assertSame($keys, array_keys($table->getData()));
        }
    }

    public function test_upsert()
    {
        $table = new ArrayTable(self::DATA, 'id');

        $table->upsert(['id' => 2, 'name' => 'Purple']);
        self::assertSame(['id' => 2, 'name' => 'Purple'], $table->get(2));

        $table->upsert(['name' => 'White']);
        self::assertSame(['name' => 'White', 'id' => 6], $table->get(6));
    }
}
