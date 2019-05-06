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

class ParentField implements FieldInterface
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
    private $parentTable;
    /**
     * @var string
     */
    private $parentField;
    /**
     * @var bool
     */
    private $selectAll = true;
    /**
     * @var bool
     */
    private $selectArray = false;

    /**
     * @param string               $name        current table foreign key
     * @param Table                $parentTable parent table object
     * @param null|string|callable $parentField used for Expression / Filtering
     * @param null|string          $selectAlias alias used in select (default will be table name)
     */
    public function __construct(string $name, Table $parentTable, $parentField, ?string $selectAlias = null)
    {
        $this->name        = $name;
        $this->parentField = $parentField;
        $this->parentTable = $parentTable;
        $this->selectAlias = $selectAlias ?: $parentTable->getName();
        $this->selectAll   = \is_object($parentField) && \is_callable($parentField) ? true : ($parentField ? false : true);
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

    public function getParentField(): string
    {
        return $this->parentField;
    }

    public function getParentTable(): Table
    {
        return $this->parentTable;
    }

    public function value(array $row, $key = null)
    {
        if (isset($row[$this->name])) {
            $foreignRow = $this->parentTable->get($row[$this->name]);
            if (\is_object($this->parentField) && \is_callable($this->parentField)) {
                return \call_user_func($this->parentField, $foreignRow, $row[$this->name]);
            } elseif ($this->parentField !== null) {
                return $foreignRow[$this->parentField] ?? null;
            }
        }
        return null;
    }

    public function select(array $row, $key = null): array
    {
        if (isset($row[$this->name])) {
            $foreignRow = $this->parentTable->get($row[$this->name]);

            if (!$this->selectAll) {
                if (\is_object($this->parentField) && \is_callable($this->parentField)) {
                    return \call_user_func($this->parentField, $foreignRow, $row[$this->name]);
                } else {
                    $value = $foreignRow[$this->parentField] ?? null;
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
