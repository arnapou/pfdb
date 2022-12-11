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

use Arnapou\PFDB\Core\AbstractTable;
use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Exception\InvalidTableClassException;
use Arnapou\PFDB\Storage\StorageInterface;
use Arnapou\PFDB\Table;
use TypeError;

/**
 * All the children classes of this abstract work with an AbstractTable class.
 */
abstract class AbstractTableFactory implements TableFactoryInterface
{
    private string $tableClass;

    public function __construct()
    {
        $this->tableClass = Table::class;
    }

    public function getTableClass(): string
    {
        return $this->tableClass;
    }

    public function setTableClass(string $tableClass): self
    {
        $this->checkTableClass($tableClass);
        $this->tableClass = $tableClass;

        return $this;
    }

    public function isTableClassValid(string $tableClass): bool
    {
        return is_subclass_of($tableClass, AbstractTable::class);
    }

    public function checkTableClass(string $tableClass): void
    {
        if (!$this->isTableClassValid($tableClass)) {
            throw new InvalidTableClassException('This factory works with classes child of built-in AbstractTable');
        }
    }

    protected function createInstance(
        StorageInterface $storage,
        string $name,
        ?string $primaryKey
    ): TableInterface {
        $this->checkTableClass($class = $this->getTableClass());

        $table = new $class($storage, $name, $primaryKey);
        if (!$table instanceof TableInterface) {
            throw new TypeError('The table is not a valid TableInterface object');
        }

        return $table;
    }
}
