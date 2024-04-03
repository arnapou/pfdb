<?php

declare(strict_types=1);

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
use Closure;

/**
 * Complex "parent" field which returns
 * - value : the parent matching field
 * - select : a multiple field array to simulates joins.
 *
 * Callable Arguments must follow these signatures :
 * - `$name` + `$parentField` :
 * <pre>
 * function(array $row, int|string|null $key = null): string|int|float|bool|null|array {
 *     // compute $value
 *     return $value;
 * }
 * </pre>
 * - `$parentRow` :
 * <pre>
 * function(int|string $key, TableInterface $parentTable): array {
 *     // compute $parentRow using the table and key
 *     return $parentRow;
 * }
 * </pre>
 */
class ParentField implements FieldValueInterface, FieldSelectInterface
{
    use SanitizeHelperTrait;

    private readonly string $selectAlias;
    private bool $selectAll = true;
    private bool $selectArray = false;
    private readonly Closure $name;
    private readonly ?Closure $parentField;
    private readonly ?Closure $parentRow;

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
    public function __construct(
        string|FieldValueInterface|callable $name,
        private readonly TableInterface $parentTable,
        string|FieldValueInterface|callable|null $parentField = null,
        ?string $selectAlias = null,
        ?callable $parentRow = null
    ) {
        $this->name = $this->sanitizeField($name);
        $this->parentField = null === $parentField ? null : $this->sanitizeField($parentField);
        $this->selectAlias = (null === $selectAlias || '' === $selectAlias) ? $parentTable->getName() : $selectAlias;
        $this->selectAll = null === $parentField;
        $this->parentRow = null === $parentRow ? null : $parentRow(...);
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

    public function name(): Closure
    {
        return $this->name;
    }

    public function getSelectAlias(): ?string
    {
        return $this->selectAlias;
    }

    public function getParentField(): ?Closure
    {
        return $this->parentField;
    }

    public function getParentTable(): TableInterface
    {
        return $this->parentTable;
    }

    public function value(array $row, string|int|null $key = null): string|int|float|bool|array|null
    {
        if (null !== $this->parentField) {
            $value = ($this->name)($row, $key);
            if (null !== $value) {
                $parentRow = null === $this->parentRow
                    ? $this->parentTable->get($value)
                    : ($this->parentRow)($value, $this->parentTable);
                if (null === $parentRow) {
                    return null;
                }

                return ($this->parentField)($parentRow, $value);
            }
        }

        return null;
    }

    public function select(array $row, string|int|null $key = null): array
    {
        $value = ($this->name)($row, $key);

        if (null === $value) {
            return [$this->selectAlias => null];
        }

        $parentRow = null === $this->parentRow
            ? $this->parentTable->get($value)
            : ($this->parentRow)($value, $this->parentTable);

        if (null === $parentRow) {
            return [$this->selectAlias => null];
        }

        if ($this->selectAll) {
            if ($this->selectArray) {
                return [$this->selectAlias => $parentRow];
            }

            $values = [];
            foreach ($parentRow as $k => $v) {
                $values[$this->selectAlias . '_' . $k] = $v;
            }

            return $values;
        }

        if (null === $this->parentField) {
            // @codeCoverageIgnoreStart
            // Theoretically not reachable because of logic inside selectAll().
            // This exists for static analysis purpose only.
            return [$this->selectAlias => null];
            // @codeCoverageIgnoreEnd
        }

        if ($this->selectArray) {
            $value = ($this->parentField)($parentRow, $value);
            if (!\is_array($value)) {
                throw new InvalidCallableException('The specified callable for the parent field should return an array :(');
            }

            return $value;
        }

        return [$this->selectAlias => ($this->parentField)($parentRow, $value)];
    }
}
