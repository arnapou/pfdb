<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Factory;

use Arnapou\PFDB\Database;
use Arnapou\PFDB\Exception\InvalidTableClassException;
use Arnapou\PFDB\Factory\StaticPKTableFactory;
use Arnapou\PFDB\Table;
use PHPUnit\Framework\TestCase;

class StaticPKTableFactoryTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $factory = new StaticPKTableFactory('id');

        self::assertSame('id', $factory->getDefaultPrimaryKey());
        self::assertSame('pk', $factory->setDefaultPrimaryKey('pk')->getDefaultPrimaryKey());

        self::assertSame(Table::class, $factory->getTableClass());
        self::assertSame(Table::class, $factory->setTableClass(Table::class)->getTableClass());
    }

    public function testSetTableException(): void
    {
        $factory = new StaticPKTableFactory();

        $this->expectException(InvalidTableClassException::class);

        $factory->setTableClass(Database::class);
    }
}
