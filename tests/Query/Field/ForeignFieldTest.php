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

use Arnapou\PFDB\Query\Field\ParentField;
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

    public function test_getters()
    {
        $foreignTable = self::foreignTable();
        $field        = new ParentField('fkid', $foreignTable, 'name');
        $this->assertSame('fkid', $field->name());
        $this->assertSame('name', $field->getParentField());
        $this->assertSame($foreignTable, $field->getParentTable());
        $this->assertSame($foreignTable->getName(), $field->getSelectAlias());

        $this->assertFalse($field->isSelectAll());
        $this->assertFalse($field->isSelectArray());
        $this->assertTrue($field->selectAll(true)->isSelectAll());
        $this->assertTrue($field->selectArray(true)->isSelectArray());
    }

    public function test_default_select_all_if_parent_field_is_a_callable()
    {
        $field = new ParentField('fkid', self::foreignTable(), function ($row, $key) {
            return $row['id'] . ':' . $row['name'];
        });
        $this->assertTrue($field->isSelectAll());
    }

    public function test_value_usage()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');

        $this->assertSame('Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function test_value_always_null_when_parent_field_is_null()
    {
        $field = new ParentField('fkid', self::foreignTable(), null);

        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_value_usage_with_callable()
    {
        $field = new ParentField('fkid', self::foreignTable(), function ($row, $key) {
            return $row['id'] . ':' . $row['name'];
        });

        $this->assertSame('2:Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_value_unknown_foreign_key()
    {
        $field = new ParentField('unknown_fkid', self::foreignTable(), 'name');

        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_default_select_usage()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');

        $this->assertSame(['color' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function test_select_with_callable()
    {
        $field = new ParentField('fkid', self::foreignTable(), function ($row, $key) {
            return ['id:color' => $row['id'] . ':' . $row['name']];
        });
        $field->selectAll(false);

        $this->assertSame(['id:color' => '2:Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_select_renamed()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name', 'renamed');

        $this->assertSame(['renamed' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['renamed' => null], $field->select(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function test_select_parent_as_flat()
    {
        $field = new ParentField('fkid', self::foreignTable(), null);

        $this->assertSame(
            [
                'color_id'   => 2,
                'color_name' => 'Green',
            ],
            $field->select(['toy' => 'balloon', 'fkid' => 2])
        );
    }

    public function test_select_parent_as_an_array()
    {
        $field = new ParentField('fkid', self::foreignTable(), null);
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
