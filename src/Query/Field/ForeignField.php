<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Field;

use Arnapou\PFDB\Table;

class ForeignField implements FieldInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $selectAlias;
    /**
     * @var Table
     */
    private $foreignTable;
    /**
     * @var string
     */
    private $foreignName;
    /**
     * @var bool
     */
    private $selectAll = true;
    /**
     * @var bool
     */
    private $selectArray = false;

    /**
     * ForeignField constructor.
     * @param string               $name         current table foreign key
     * @param Table                $foreignTable foreign table object
     * @param null|string|callable $foreignName  used for Expression / Filtering
     * @param null|string          $selectAlias  alias used in select (default will be table name)
     */
    public function __construct(string $name, Table $foreignTable, $foreignName, ?string $selectAlias = null)
    {
        $this->name         = $name;
        $this->foreignName  = $foreignName;
        $this->foreignTable = $foreignTable;
        $this->selectAlias  = $selectAlias ?: $foreignTable->getName();
        $this->selectAll    = \is_object($foreignName) && \is_callable($foreignName) ? true : ($foreignName ? false : true);
    }

    public function selectAll(bool $all = true): self
    {
        $this->selectAll = $all;
        return $this;
    }

    public function isSelectAll(): bool
    {
        return $this->selectAll;
    }

    public function selectArray(bool $array = true): self
    {
        $this->selectArray = $array;
        return $this;
    }

    public function isSelectArray(): bool
    {
        return $this->selectArray;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function getSelectAlias(): ?string
    {
        return $this->selectAlias;
    }

    public function getForeignName(): string
    {
        return $this->foreignName;
    }

    public function getForeignTable(): Table
    {
        return $this->foreignTable;
    }

    public function value(array $row, $key = null)
    {
        if (isset($row[$this->name])) {
            $foreignRow = $this->foreignTable->get($row[$this->name]);
            if (\is_object($this->foreignName) && \is_callable($this->foreignName)) {
                return \call_user_func($this->foreignName, $foreignRow, $row[$this->name]);
            } elseif ($this->foreignName !== null) {
                return $foreignRow[$this->foreignName] ?? null;
            }
        }
        return null;
    }

    public function select(array $row, $key = null): array
    {
        if (isset($row[$this->name])) {
            $foreignRow = $this->foreignTable->get($row[$this->name]);

            if (!$this->selectAll) {
                if (\is_object($this->foreignName) && \is_callable($this->foreignName)) {
                    return \call_user_func($this->foreignName, $foreignRow, $row[$this->name]);
                } else {
                    $value = $foreignRow[$this->foreignName] ?? null;
                }
                return [$this->selectAlias => $value];
            } elseif ($this->selectArray) {
                return [$this->selectAlias => $foreignRow];
            } else {
                $values = [];
                foreach ($foreignRow as $key => $value) {
                    $values[$this->selectAlias . '_' . $key] = $value;
                }
                return $values;
            }
        }
        return [$this->selectAlias => null];
    }
}
