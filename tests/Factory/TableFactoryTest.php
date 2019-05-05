<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Factory;

use Arnapou\PFDB\Database;
use Arnapou\PFDB\Exception\InvalidTableClassException;
use Arnapou\PFDB\Factory\TableFactory;
use Arnapou\PFDB\Table;
use PHPUnit\Framework\TestCase;

class TableFactoryTest extends TestCase
{
    public function testGetterSetter()
    {
        $factory = new TableFactory('id');

        $this->assertSame('id', $factory->getDefaultPrimaryKey());
        $this->assertSame('pk', $factory->setDefaultPrimaryKey('pk')->getDefaultPrimaryKey());

        $this->assertSame(Table::class, $factory->getTableClass());
        $this->assertSame(Table::class, $factory->setTableClass(Table::class)->getTableClass());
    }

    public function testTableClassException()
    {
        $factory = new TableFactory();

        $this->expectException(InvalidTableClassException::class);

        $factory->setTableClass(Database::class);
    }
}
