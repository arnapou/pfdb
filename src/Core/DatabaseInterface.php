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

namespace Arnapou\PFDB\Core;

use Arnapou\PFDB\Storage\StorageInterface;

/**
 * Generic Database Interface.
 */
interface DatabaseInterface
{
    /**
     * Return a list of tables of this database.
     *
     * @return TableInterface[]
     */
    public function getTables(): array;

    /**
     * Return a list of table names.
     *
     * @return string[]
     */
    public function getTableNames(): array;

    /**
     * Gets the table for a specific name.
     *
     * By default, the table is auto created thanks to a TableFactoryInterface.
     */
    public function getTable(string $name): TableInterface;

    /**
     * Get the storage object for all tables.
     */
    public function getStorage(): StorageInterface;

    /**
     * Remove a table.
     */
    public function dropTable(TableInterface $table): self;
}
