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

namespace Arnapou\PFDB\Tests\Factory;

use Arnapou\PFDB\Factory\NoPKTableFactory;
use Arnapou\PFDB\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class NoPKTableFactoryTest extends TestCase
{
    public function testBasic(): void
    {
        $factory = new NoPKTableFactory();

        $table = $factory->create(new ArrayStorage(), 'table');
        self::assertNull($table->getPrimaryKey());
        self::assertSame('table', $table->getName());
    }
}
