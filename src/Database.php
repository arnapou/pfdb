<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB;

use Arnapou\PFDB\Query\Helper\ExprTrait;
use Arnapou\PFDB\Storage\StorageInterface;

class Database
{
    use ExprTrait;

    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var Table[]
     */
    private $tables = [];
    /**
     * @var string
     */
    private $defaultPrimaryKey;
    /**
     * @var bool
     */
    private $readonly = null;

    public function __construct(StorageInterface $storage, ?string $defaultPrimaryKey = 'id')
    {
        $this->storage           = $storage;
        $this->defaultPrimaryKey = $defaultPrimaryKey;
    }

    public function getTable(string $name, ?string $primaryKey = null): Table
    {
        if (!\array_key_exists($name, $this->tables)) {
            $table = new Table($name, $this->storage, $primaryKey ?: $this->defaultPrimaryKey);
            if (null !== $this->readonly) {
                $table->setReadonly($this->readonly);
            }
            $this->tables[$name] = $table;
            ksort($this->tables);
        }
        return $this->tables[$name];
    }

    /**
     * @return Table[]
     */
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

    public function dropTable(string $tableName): self
    {
        $this->storage->delete($tableName);
        return $this;
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function getDefaultPrimaryKey(): ?string
    {
        return $this->defaultPrimaryKey;
    }

    public function isReadonly(): ?bool
    {
        return $this->readonly;
    }

    public function setReadonly(bool $readonly): self
    {
        $this->readonly = $readonly;
        foreach ($this->tables as $table) {
            $table->setReadonly($readonly);
        }
        return $this;
    }
}
