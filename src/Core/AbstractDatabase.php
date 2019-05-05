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

use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Factory\TableFactory;
use Arnapou\PFDB\Factory\TableFactoryInterface;
use Arnapou\PFDB\Query\Helper\ExprTrait;
use Arnapou\PFDB\Storage\StorageInterface;

abstract class AbstractDatabase implements DatabaseInterface
{
    use ExprTrait;

    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var TableFactoryInterface
     */
    private $tableFactory;
    /**
     * @var TableInterface[]
     */
    private $tables = [];

    public function __construct(StorageInterface $storage, ?TableFactoryInterface $tableFactory = null)
    {
        $this->storage      = $storage;
        $this->tableFactory = $tableFactory ?: new TableFactory('id');
    }

    public function getTable(string $name, ?string $primaryKey = null): TableInterface
    {
        if (!\array_key_exists($name, $this->tables)) {
            $this->tables[$name] = $this->tableFactory->create($this->storage, $name);
            ksort($this->tables);
        }
        return $this->tables[$name];
    }

    public function getTables(): array
    {
        foreach ($this->getTableNames() as $name) {
            $this->getTable($name);
        }
        return $this->tables;
    }

    public function getTableNames(): array
    {
        $names = $this->storage->tableNames();
        sort($names);
        return $names;
    }

    public function dropTable(TableInterface $table): self
    {
        if ($table->isReadonly()) {
            throw new ReadonlyException();
        }
        $this->storage->delete($table->getName());
        if (\array_key_exists($table->getName(), $this->tables)) {
            unset($this->tables[$table->getName()]);
        }
        return $this;
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }
}
