<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Storage;

use Arnapou\PFDB\DatabaseReadonly;
use Arnapou\PFDB\Storage\ArrayStorage;
use Arnapou\PFDB\Storage\ReadonlyStorage;
use PHPUnit\Framework\TestCase;

class DatabaseReadonlyTest extends TestCase
{
    public function testMisc()
    {
        $database = new DatabaseReadonly(new ArrayStorage());
        $this->assertInstanceOf(ReadonlyStorage::class, $database->getStorage());
    }
}