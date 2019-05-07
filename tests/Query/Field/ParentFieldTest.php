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

use Arnapou\PFDB\Exception\InvalidCallableException;
use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Query\Field\ParentField;
use Arnapou\PFDB\Storage\ReadonlyStorage;
use Arnapou\PFDB\Table;
use Arnapou\PFDB\Tests\Storage\PhpFileStorageTest;
use PHPUnit\Framework\TestCase;

class ParentFieldTest extends TestCase
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

        $this->assertIsCallable($field->name());
        $this->assertSame(42, \call_user_func($field->name(), ['fkid' => 42]));
        $this->assertNull(\call_user_func($field->name(), ['xxx' => 42]));

        $this->assertIsCallable($field->getParentField());
        $this->assertSame(42, \call_user_func($field->getParentField(), ['name' => 42]));
        $this->assertNull(\call_user_func($field->getParentField(), ['xxx' => 42]));

        $this->assertSame($foreignTable, $field->getParentTable());
        $this->assertSame($foreignTable->getName(), $field->getSelectAlias());

        $this->assertFalse($field->isSelectAll());
        $this->assertFalse($field->isSelectArray());
        $this->assertTrue($field->selectAll(true)->isSelectAll());
        $this->assertTrue($field->selectArray(true)->isSelectArray());

        $field = new ParentField('fkid', $foreignTable);
        $this->assertNull($field->getParentField());
    }

    public function test_parent_field_is_a_string()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');

        $this->assertSame('Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_parent_field_is_null()
    {
        $field = new ParentField('fkid', self::foreignTable(), null);

        $this->assertNull($field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color_id' => 2, 'color_name' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_parent_field_is_a_callable()
    {
        $field = new ParentField('fkid', self::foreignTable(), function ($row, $key) {
            return $row['id'] . ':' . $row['name'];
        });

        $this->assertSame('2:Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => '2:Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_select_alias()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $this->assertSame(self::foreignTable()->getName(), $field->getSelectAlias());

        $field = new ParentField('fkid', self::foreignTable(), 'name', 'foo_bar');
        $this->assertSame('foo_bar', $field->getSelectAlias());
    }

    public function test_inconsistent_no_parent_field_and_no_select_all()
    {
        $field = new ParentField('fkid', self::foreignTable());
        $this->expectException(InvalidFieldException::class);
        $field->selectAll(false);
    }

    public function test_unknown_foreign_key()
    {
        $field = new ParentField('unknown_fkid', self::foreignTable(), 'name');
        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 2]));
        $this->assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 2]));

        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $this->assertSame(null, $field->value(['toy' => 'balloon', 'fkid' => 99]));
        $this->assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function test_select_all_and_NOT_select_array()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(true)->selectArray(false);

        $this->assertSame(['color_id' => 2, 'color_name' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_select_all_and_select_array()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(true)->selectArray(true);

        $this->assertSame(['color' => ['id' => 2, 'name' => 'Green']], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_NOT_select_all_and_NOT_select_array()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(false)->selectArray(false);

        $this->assertSame(['color' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_NOT_select_all_and_select_array()
    {
        $field = new ParentField('fkid', self::foreignTable(), function ($row, $key) {
            return ['row' => $row, 'key' => $key];
        });
        $field->selectAll(false)->selectArray(true);

        $this->assertSame(['row' => ['id' => 2, 'name' => 'Green'], 'key' => 2], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function test_NOT_select_all_and_select_array_raises_exception_if_parent_field_is_not_a_specific_callable()
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(false)->selectArray(true);

        $this->expectException(InvalidCallableException::class);
        $this->assertSame(['row' => ['id' => 2, 'name' => 'Green'], 'key' => 2], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }
}
