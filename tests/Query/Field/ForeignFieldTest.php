<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query\Field;

use Arnapou\PFDB\Query\Field\ForeignField;
use Arnapou\PFDB\Storage\ReadonlyStorage;
use Arnapou\PFDB\Table;
use Arnapou\PFDB\Tests\Storage\PhpFileStorageTest;
use PHPUnit\Framework\TestCase;

class ForeignFieldTest extends TestCase
{
    public static function foreignTable(): Table
    {
        $storage = new ReadonlyStorage(PhpFileStorageTest::pfdbStorage());
        return new Table($storage, 'color', 'id');
    }

    public function testGetters()
    {
        $foreignTable = self::foreignTable();
        $field        = new ForeignField('fkid', $foreignTable, 'name');
        $this->assertSame('fkid', $field->name());
        $this->assertSame('name', $field->getForeignName());
        $this->assertSame($foreignTable, $field->getForeignTable());
        $this->assertSame($foreignTable->getName(), $field->getSelectAlias());

        $this->assertFalse($field->isSelectAll());
        $this->assertFalse($field->isSelectArray());
        $this->assertTrue($field->selectAll(true)->isSelectAll());
        $this->assertTrue($field->selectArray(true)->isSelectArray());
    }

    public function testDefaultSelectAllIfForeignNameIsACallable()
    {
        $field = new ForeignField('fkid', self::foreignTable(), function ($row, $key) {
            return $row['id'] . ':' . $row['name'];
        });
        $this->assertTrue($field->isSelectAll());
    }

    public function testValueUsage()
    {
        $field = new ForeignField('fkid', self::foreignTable(), 'name');

        $this->assertSame('Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function testValueAlwaysNullWhenForeignNameIsNull()
    {
        $field = new ForeignField('fkid', self::foreignTable(), null);

        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testValueUsageWithCallable()
    {
        $field = new ForeignField('fkid', self::foreignTable(), function ($row, $key) {
            return $row['id'] . ':' . $row['name'];
        });

        $this->assertSame('2:Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testValueUnknownFK()
    {
        $field = new ForeignField('unknown_fkid', self::foreignTable(), 'name');

        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testDefaultSelectUsage()
    {
        $field = new ForeignField('fkid', self::foreignTable(), 'name');

        $this->assertSame(['color' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function testSelectWithCallable()
    {
        $field = new ForeignField('fkid', self::foreignTable(), function ($row, $key) {
            return ['id:color' => $row['id'] . ':' . $row['name']];
        });
        $field->selectAll(false);

        $this->assertSame(['id:color' => '2:Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testSelectRenamed()
    {
        $field = new ForeignField('fkid', self::foreignTable(), 'name', 'renamed');

        $this->assertSame(['renamed' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['renamed' => null], $field->select(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function testSelectForeignAsFlat()
    {
        $field = new ForeignField('fkid', self::foreignTable(), null);

        $this->assertSame(
            [
                'color_id'   => 2,
                'color_name' => 'Green',
            ],
            $field->select(['toy' => 'balloon', 'fkid' => 2])
        );
    }

    public function testSelectForeignAsArray()
    {
        $field = new ForeignField('fkid', self::foreignTable(), null);
        $field->selectArray(true);

        $this->assertSame(
            [
                'color' =>
                    [
                        'id'   => 2,
                        'name' => 'Green',
                    ],
            ],
            $field->select(['toy' => 'balloon', 'fkid' => 2])
        );
    }
}
