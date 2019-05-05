<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Core;

use Arnapou\PFDB\Storage\StorageInterface;

interface DatabaseInterface
{
    public function getStorage(): StorageInterface;

    public function getTable(string $name): TableInterface;

    /**
     * @return TableInterface[]
     */
    public function getTables(): array;

    public function getTableNames(): array;

    public function dropTable(TableInterface $table);
}
