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

class TableFactory implements TableFactoryInterface
{
    /**
     * @var string
     */
    private $tableClass;
    /**
     * @var string|null
     */
    private $defaultPrimaryKey;

    public function __construct(?string $defaultPrimaryKey = 'id')
    {
        $this->defaultPrimaryKey = $defaultPrimaryKey;
        $this->tableClass        = Table::class;
    }

    public function getTableClass(): string
    {
        return $this->tableClass;
    }

    public function setTableClass(string $tableClass): self
    {
        if (!is_subclass_of($tableClass, AbstractTable::class)) {
            throw new InvalidTableClassException('This factory works with classes child of built-in AbstractTable');
        }
        $this->tableClass = $tableClass;
        return $this;
    }

    public function getDefaultPrimaryKey(): ?string
    {
        return $this->defaultPrimaryKey;
    }

    public function setDefaultPrimaryKey(?string $defaultPrimaryKey): self
    {
        $this->defaultPrimaryKey = $defaultPrimaryKey;
        return $this;
    }

    public function create(StorageInterface $storage, string $name): TableInterface
    {
        $class = $this->tableClass;
        return new $class($storage, $name, $this->defaultPrimaryKey);
    }
}
