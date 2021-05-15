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

use Arnapou\PFDB\Factory\DynamicPKTableFactory;
use Arnapou\PFDB\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class DynamicPKTableFactoryTest extends TestCase
{
    public function test_no_callable()
    {
        $factory = new DynamicPKTableFactory();

        $table = $factory->create(new ArrayStorage(), 'table');
        self::assertSame('idtable', $table->getPrimaryKey());
        self::assertSame('table', $table->getName());
    }

    public function test_with_callable()
    {
        $pkFactory = function ($name) {
            return $name . '_pk';
        };
        $factory = new DynamicPKTableFactory($pkFactory);

        $table = $factory->create(new ArrayStorage(), 'table');
        self::assertSame('table_pk', $table->getPrimaryKey());
        self::assertSame('table', $table->getName());

        $pkFactory = function ($name) {
            return $name . '_id';
        };
        $factory->setPkFactory($pkFactory);
        self::assertSame($pkFactory, $factory->getPkFactory());
    }
}
