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

use Arnapou\PFDB\Exception\InvalidCallableException;
use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Query\Helper\SanitizeHelperTrait;
use Arnapou\PFDB\Table;

class ParentField implements FieldInterface
{
    use SanitizeHelperTrait;

    /**
     * @var callable
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
     * @var callable|null
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
     * @param string|FieldValueInterface|callable      $name        current table foreign key
     * @param Table                                    $parentTable parent table object
     * @param string|FieldValueInterface|callable|null $parentField used for Expression / Filtering
     * @param null|string                              $selectAlias alias used in select (default will be table name)
     * @throws InvalidFieldException
     */
    public function __construct($name, Table $parentTable, $parentField = null, ?string $selectAlias = null)
    {
        $this->name        = $this->sanitizeField($name);
        $this->parentTable = $parentTable;
        $this->parentField = $parentField === null ? null : $this->sanitizeField($parentField);
        $this->selectAlias = $selectAlias ?: $parentTable->getName();
        $this->selectAll   = $parentField === null ? true : false;
    }

    public function selectAll(bool $all = true): self
    {
        if (null === $this->parentField && !$all) {
            throw new InvalidFieldException('This is not consistent to want only one parent field without specifying it');
        }
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

    public function name(): callable
    {
        return $this->name;
    }

    public function getSelectAlias(): ?string
    {
        return $this->selectAlias;
    }

    public function getParentField(): ?callable
    {
        return $this->parentField;
    }

    public function getParentTable(): Table
    {
        return $this->parentTable;
    }

    public function value(array $row, $key = null)
    {
        if ($this->parentField) {
            $value = \call_user_func($this->name, $row, $key);
            if (null !== $value) {
                $parentRow = $this->parentTable->get($value);
                if (null === $parentRow) {
                    return null;
                }
                return \call_user_func($this->parentField, $parentRow, $value);
            }
        }
        return null;
    }

    public function select(array $row, $key = null): array
    {
        $value = \call_user_func($this->name, $row, $key);
        if (null !== $value) {
            $parentRow = $this->parentTable->get($value);
            if (null === $parentRow) {
                return [$this->selectAlias => null];
            }
            switch (true) {
                case $this->selectAll && $this->selectArray:
                    return [$this->selectAlias => $parentRow];
                case $this->selectAll && !$this->selectArray:
                    $values = [];
                    foreach ($parentRow as $key => $value) {
                        $values[$this->selectAlias . '_' . $key] = $value;
                    }
                    return $values;
                case !$this->selectAll && $this->selectArray:
                    $value = \call_user_func($this->parentField, $parentRow, $value);
                    if (!\is_array($value)) {
                        throw new InvalidCallableException('The specified callable for the parent field should return an array :(');
                    }
                    return $value;
                case !$this->selectAll && !$this->selectArray:
                    return [$this->selectAlias => \call_user_func($this->parentField, $parentRow, $value)];
            }
        }
        return [$this->selectAlias => null];
    }
}
