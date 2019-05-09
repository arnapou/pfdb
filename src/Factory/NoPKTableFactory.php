<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Factory;

use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Storage\StorageInterface;
use Arnapou\PFDB\Table;

class NoPKTableFactory extends AbstractTableFactory
{
    public function __construct()
    {
        $this->setTableClass(Table::class);
    }

    public function create(StorageInterface $storage, string $name): TableInterface
    {
        $class = $this->getTableClass();
        return new $class($storage, $name, null);
    }
}