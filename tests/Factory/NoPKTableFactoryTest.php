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

use Arnapou\PFDB\Factory\NoPKTableFactory;
use Arnapou\PFDB\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class NoPKTableFactoryTest extends TestCase
{
    public function test_basic()
    {
        $factory = new NoPKTableFactory();

        $table = $factory->create(new ArrayStorage(), 'table');
        $this->assertNull($table->getPrimaryKey());
        $this->assertSame('table', $table->getName());
    }
}
