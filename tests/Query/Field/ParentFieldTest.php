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

namespace Arnapou\PFDB\Tests\Query\Field;

use Arnapou\PFDB\ArrayTable;
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

    public function testParentFieldNull(): void
    {
        $field = new ParentField(
            fn ($row, $key) => 'my_id',
            new ArrayTable([['id' => 'my_id']], 'id'),
            fn () => null,
            'the_alias'
        );
        $field->selectAll(false);
        self::assertSame(
            ['the_alias' => null],
            $field->select(['any'], 'any')
        );
    }

    public function testGetters(): void
    {
        $foreignTable = self::foreignTable();
        $field = new ParentField('fkid', $foreignTable, 'name');

        self::assertSame(42, \call_user_func($field->name(), ['fkid' => 42]));
        self::assertNull(\call_user_func($field->name(), ['xxx' => 42]));

        self::assertIsCallable($field->getParentField());
        self::assertSame(42, \call_user_func($field->getParentField(), ['name' => 42]));
        self::assertNull(\call_user_func($field->getParentField(), ['xxx' => 42]));

        self::assertSame($foreignTable, $field->getParentTable());
        self::assertSame($foreignTable->getName(), $field->getSelectAlias());

        self::assertFalse($field->isSelectAll());
        self::assertFalse($field->isSelectArray());
        self::assertTrue($field->selectAll(true)->isSelectAll());
        self::assertTrue($field->selectArray(true)->isSelectArray());

        $field = new ParentField('fkid', $foreignTable);
        self::assertNull($field->getParentField());
    }

    public function testParentFieldIsAString(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');

        self::assertSame('Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
        self::assertSame(['color' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testParentFieldIsNull(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), null);

        self::assertNull($field->value(['toy' => 'balloon', 'fkid' => 2]));
        self::assertSame(['color_id' => 2, 'color_name' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testParentFieldIsACallable(): void
    {
        $field = new ParentField(
            'fkid',
            self::foreignTable(),
            function ($row, $key) {
                return $row['id'] . ':' . $row['name'];
            }
        );

        self::assertSame('2:Green', $field->value(['toy' => 'balloon', 'fkid' => 2]));
        self::assertSame(['color' => '2:Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testSelectAlias(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        self::assertSame(self::foreignTable()->getName(), $field->getSelectAlias());

        $field = new ParentField('fkid', self::foreignTable(), 'name', 'foo_bar');
        self::assertSame('foo_bar', $field->getSelectAlias());
    }

    public function testInconsistentNoParentFieldAndNoSelectAll(): void
    {
        $field = new ParentField('fkid', self::foreignTable());
        $this->expectException(InvalidFieldException::class);
        $field->selectAll(false);
    }

    public function testUnknownForeignKey(): void
    {
        $field = new ParentField('unknown_fkid', self::foreignTable(), 'name');
        self::assertNull($field->value(['toy' => 'balloon', 'fkid' => 2]));
        self::assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 2]));

        $field = new ParentField('fkid', self::foreignTable(), 'name');
        self::assertNull($field->value(['toy' => 'balloon', 'fkid' => 99]));
        self::assertSame(['color' => null], $field->select(['toy' => 'balloon', 'fkid' => 99]));
    }

    public function testSelectAllAndNotSelectArray(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(true)->selectArray(false);

        self::assertSame(['color_id' => 2, 'color_name' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testSelectAllAndSelectArray(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(true)->selectArray(true);

        self::assertSame(['color' => ['id' => 2, 'name' => 'Green']], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testNotSelectAllAndNotSelectArray(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(false)->selectArray(false);

        self::assertSame(['color' => 'Green'], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testNotSelectAllAndSelectArray(): void
    {
        $field = new ParentField(
            'fkid',
            self::foreignTable(),
            function ($row, $key) {
                return ['row' => $row, 'key' => $key];
            }
        );
        $field->selectAll(false)->selectArray(true);

        self::assertSame(['row' => ['id' => 2, 'name' => 'Green'], 'key' => 2], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testNotSelectAllAndSelectArrayRaisesExceptionIfParentFieldIsNotASpecificCallable(): void
    {
        $field = new ParentField('fkid', self::foreignTable(), 'name');
        $field->selectAll(false)->selectArray(true);

        $this->expectException(InvalidCallableException::class);
        self::assertSame(['row' => ['id' => 2, 'name' => 'Green'], 'key' => 2], $field->select(['toy' => 'balloon', 'fkid' => 2]));
    }

    public function testParentRowInValueMethod(): void
    {
        $field = new ParentField(
            'fkid',
            self::foreignTable(),
            'name',
            null,
            function ($value) {
                return self::foreignTable()->get($value + 1);
            }
        );
        self::assertSame('Blue', $field->value(['toy' => 'balloon', 'fkid' => 2]));

        $field = new ParentField(
            function ($row, $key) {
                return $row['10times_fkid'] / 10;
            },
            self::foreignTable(),
            function ($row, $key) {
                return 'The color is ' . $row['name'];
            },
            null,
            function ($value) {
                return self::foreignTable()->get($value + 1);
            }
        );
        self::assertSame('The color is Blue', $field->value(['toy' => 'balloon', '10times_fkid' => 20]));
    }

    public function testParentRowInSelectMethod(): void
    {
        $field = new ParentField(
            'fkid',
            self::foreignTable(),
            'name',
            null,
            function ($value) {
                return self::foreignTable()->get($value + 1);
            }
        );
        self::assertSame(['color' => 'Blue'], $field->select(['toy' => 'balloon', 'fkid' => 2]));

        $field = new ParentField(
            function ($row, $key) {
                return $row['10times_fkid'] / 10;
            },
            self::foreignTable(),
            function ($row, $key) {
                return 'The color is ' . $row['name'];
            },
            null,
            function ($value) {
                return self::foreignTable()->get($value + 1);
            }
        );
        self::assertSame(['color' => 'The color is Blue'], $field->select(['toy' => 'balloon', '10times_fkid' => 20]));
    }
}
