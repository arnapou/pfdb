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
use Arnapou\PFDB\Factory\StaticPKTableFactory;
use Arnapou\PFDB\Factory\TableFactoryInterface;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Helper\FieldsHelperTrait;
use Arnapou\PFDB\Storage\StorageInterface;

abstract class AbstractDatabase implements DatabaseInterface
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    /**
     * @var TableInterface[]
     */
    private array $tables = [];

    public function __construct(
        private StorageInterface $storage,
        private TableFactoryInterface $tableFactory = new StaticPKTableFactory('id')
    ) {
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
