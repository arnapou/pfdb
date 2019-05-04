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

use Arnapou\PFDB\Storage\PhpFileStorage;
use Arnapou\PFDB\Storage\StorageInterface;
use Arnapou\PFDB\Tests\TestCase;

class PhpFileStorageTest extends TestCase
{
    /**
     * @var \Arnapou\PFDB\Storage\StorageInterface
     */
    protected $storage;

    protected function storage(): StorageInterface
    {
        if (!$this->storage) {
            $this->storage = new PhpFileStorage(__DIR__ . '/../../demo/database');
        }
        return $this->storage;
    }

    public function testCount()
    {
        $rows = $table = $this->storage()->load('vehicle');
        $this->assertSame(9, \count($rows));
    }
}
