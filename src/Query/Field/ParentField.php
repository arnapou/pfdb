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

use Arnapou\PFDB\Core\TableInterface;
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
     * @var string
     */
    private $selectAlias;
    /**
     * @var TableInterface
     */
    private $parentTable;
    /**
     * @var ?callable
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
     * @var ?callable
     */
    private $parentRow;

    /**
     * @param string|FieldValueInterface|callable      $name        current table foreign key
     * @param TableInterface                           $parentTable parent table object
     * @param string|FieldValueInterface|callable|null $parentField used for Expression / Filtering
     * @param string|null                              $selectAlias alias used in select (default will be table name)
     * @param callable|null                            $parentRow   default is null because it gets the parent by its "primary key"
     *                                                              (Table::get method) but you can define your own method to get the
     *                                                              parent row but in this case you must be carefull about performance !
     *
     * @throws InvalidFieldException
     */
    public function __construct($name, TableInterface $parentTable, $parentField = null, ?string $selectAlias = null, ?callable $parentRow = null)
    {
        $this->name = $this->sanitizeField($name);
        $this->parentTable = $parentTable;
        $this->parentField = null === $parentField ? null : $this->sanitizeField($parentField);
        $this->selectAlias = $selectAlias ?: $parentTable->getName();
        $this->selectAll = null === $parentField;
        $this->parentRow = $parentRow;
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

    public function getParentTable(): TableInterface
    {
        return $this->parentTable;
    }

    public function value(array $row, $key = null)
    {
        if ($this->parentField) {
            $value = \call_user_func($this->name, $row, $key);
            if (null !== $value) {
                $parentRow = null === $this->parentRow
                    ? $this->parentTable->get($value)
                    : \call_user_func($this->parentRow, $value, $this->parentTable);
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

        if (null === $value) {
            return [$this->selectAlias => null];
        }

        $parentRow = null === $this->parentRow
            ? $this->parentTable->get($value)
            : \call_user_func($this->parentRow, $value, $this->parentTable);

        if (null === $parentRow) {
            return [$this->selectAlias => null];
        }

        if ($this->selectAll) {
            if ($this->selectArray) {
                return [$this->selectAlias => $parentRow];
            }

            $values = [];
            foreach ($parentRow as $k => $value) {
                $values[$this->selectAlias . '_' . $k] = $value;
            }

            return $values;
        }

        if (null === $this->parentField) {
            return [$this->selectAlias => null];
        }

        if ($this->selectArray) {
            $value = \call_user_func($this->parentField, $parentRow, $value);
            if (!\is_array($value)) {
                throw new InvalidCallableException('The specified callable for the parent field should return an array :(');
            }

            return $value;
        }

        return [$this->selectAlias => \call_user_func($this->parentField, $parentRow, $value)];
    }
}
