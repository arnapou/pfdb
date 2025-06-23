<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Core;

use Arnapou\Ensure\Enforce;
use Arnapou\Ensure\Ensure;
use Arnapou\PFDB\ArrayTable;
use Arnapou\PFDB\Exception\MultipleActionException;
use Arnapou\PFDB\Exception\PrimaryKeyAlreadyExistsException;
use Arnapou\PFDB\Exception\PrimaryKeyNotFoundException;
use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Exception\ValueNotFoundException;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Helper\FieldsHelperTrait;
use Arnapou\PFDB\Query\Query;
use Arnapou\PFDB\Storage\StorageInterface;
use ArrayIterator;
use Closure;
use IteratorAggregate;
use Throwable;
use Traversable;

/**
 * Main table class you can extend at your needs.
 *
 * @template-implements IteratorAggregate<int|string, array>
 */
abstract class AbstractTable implements IteratorAggregate, TableInterface
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    /** @var array<array<mixed>> */
    private array $data = [];
    private bool $changed = false;
    private bool $readonly = false;
    private string|int|null $lastInsertedKey = null;
    private Closure $primaryKeyGenerator;

    /**
     * This constructor if final to be sure that it will be always constructed with these 3 arguments.
     *
     * It you need to "override" it anyway, just extend the TableDecorator (look at ArrayTable for an example).
     *
     * @see TableDecorator
     * @see ArrayTable
     */
    final public function __construct(
        private readonly StorageInterface $storage,
        private readonly string $name,
        private ?string $primaryKey,
    ) {
        $this->primaryKeyGenerator = $this->getDefaultPrimaryKeyGenerator();
        $this->load($this->storage->load($this->name));
    }

    /**
     * This is a default primary key generator you can extend.
     *
     * It is simple and "auto-increments" the key as an integer.
     */
    protected function getDefaultPrimaryKeyGenerator(): Closure
    {
        return function (): int {
            $maxKey = -1;
            foreach ($this->data as $key => $value) {
                if (ctype_digit((string) $key) && (int) $key > $maxKey) {
                    $maxKey = (int) $key;
                }
            }
            do {
                ++$maxKey;
            } while (\array_key_exists($maxKey, $this->data));

            return $maxKey;
        };
    }

    public function setPrimaryKeyGenerator(callable $callable): self
    {
        $this->primaryKeyGenerator = $callable(...);

        return $this;
    }

    /**
     * @param array<array<mixed>> $rows
     *
     * @throws PrimaryKeyNotFoundException
     */
    protected function load(array $rows): void
    {
        if (null !== ($pk = $this->getPrimaryKey())) {
            $this->data = [];
            foreach ($rows as $row) {
                if (!\array_key_exists($pk, $row)) {
                    throw new PrimaryKeyNotFoundException();
                }
                $this->data[$row[$pk]] = $row;
            }
        } else {
            $this->data = $rows;
        }
        $this->changed = false;
    }

    public function __destruct()
    {
        if (!$this->isReadonly()) {
            $this->flush();
        }
    }

    public function getLastInsertedKey(): string|int|null
    {
        return $this->lastInsertedKey;
    }

    public function isReadonly(): bool
    {
        return $this->readonly || $this->storage->isReadonly($this->name);
    }

    public function setReadonly(bool $readonly): self
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function flush(): bool
    {
        if ($this->changed) {
            if ($this->isReadonly()) {
                throw new ReadonlyException();
            }
            $this->storage->save($this->name, null !== $this->getPrimaryKey() ? array_values($this->data) : $this->data);
            $this->changed = false;

            return true;
        }

        return false;
    }

    /**
     * @param array<mixed> $value
     *
     * @throws PrimaryKeyNotFoundException
     */
    protected function retrieveKeyFromRow(array $value): int|string
    {
        $pk = $this->getPrimaryKey();
        if (null === $pk || !\array_key_exists($pk, $value)) {
            throw new PrimaryKeyNotFoundException();
        }

        return Enforce::arrayKey($value[$pk]);
    }

    public function clear(): self
    {
        $this->data = [];

        return $this;
    }

    public function find(ExprInterface ...$exprs): Query
    {
        return (new Query($this))->where(...$exprs);
    }

    public function get(int|string $key): ?array
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @return Traversable<int|string, array<mixed>>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string|null
     */
    public function getPrimaryKey(): ?string
    {
        return '' === $this->primaryKey ? null : $this->primaryKey;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function delete(int|string|null $key): self
    {
        if (null === $key || !\array_key_exists($key, $this->data)) {
            throw new ValueNotFoundException();
        }
        unset($this->data[$key]);
        $this->changed = true;

        return $this;
    }

    public function update(array $row, int|string|null $key = null): self
    {
        $key ??= $this->retrieveKeyFromRow($row);
        if (!\array_key_exists($key, $this->data)) {
            throw new ValueNotFoundException();
        }
        $this->data[$key] = array_merge($this->data[$key], $row);
        $this->changed = true;

        return $this;
    }

    public function insert(array $row, int|string|null $key = null): self
    {
        try {
            $key ??= $this->retrieveKeyFromRow($row);
            if (\array_key_exists($key, $this->data)) {
                throw new PrimaryKeyAlreadyExistsException();
            }
        } catch (PrimaryKeyNotFoundException) {
            $key = ($this->primaryKeyGenerator)();
        }
        if (null !== ($pk = $this->getPrimaryKey())) {
            $row[$pk] = $key;
        }

        $this->data[$key] = $row;
        $this->changed = true;
        $this->lastInsertedKey = Ensure::nullableArrayKey($key);

        return $this;
    }

    public function upsert(array $row, int|string|null $key = null): self
    {
        try {
            $key ??= $this->retrieveKeyFromRow($row);
        } catch (PrimaryKeyNotFoundException) {
            $key = null;
        }

        if (null !== $key && \array_key_exists($key, $this->data)) {
            return $this->update($row, $key);
        }

        return $this->insert($row, $key);
    }

    public function insertMultiple(array $rows): self
    {
        $bkupData = $this->data;
        $bkupChanged = $this->changed;
        try {
            foreach ($rows as $row) {
                $this->insert($row);
            }
        } catch (Throwable $exception) {
            $this->data = $bkupData;
            $this->changed = $bkupChanged;
            throw new MultipleActionException('Multiple insert failed, data restored', 0, $exception);
        }

        return $this;
    }

    public function updateMultiple(ExprInterface $expr, callable $function): self
    {
        $bkupData = $this->data;
        $bkupChanged = $this->changed;
        try {
            foreach ($this->data as $key => $row) {
                if ($expr($row, $key)) {
                    $this->data[$key] = Enforce::array($function($row, $key));
                }
            }
        } catch (Throwable $exception) {
            $this->data = $bkupData;
            $this->changed = $bkupChanged;
            throw new MultipleActionException('Multiple update failed, data restored', 0, $exception);
        }

        return $this;
    }

    public function deleteMultiple(ExprInterface $expr): self
    {
        $bkupData = $this->data;
        $bkupChanged = $this->changed;
        $keysToDelete = [];
        try {
            foreach ($this->data as $key => $row) {
                if ($expr($row, $key)) {
                    $keysToDelete[] = $key;
                }
            }
            foreach ($keysToDelete as $key) {
                unset($this->data[$key]);
            }
        } catch (Throwable $exception) {
            $this->data = $bkupData;
            $this->changed = $bkupChanged;
            throw new MultipleActionException('Multiple delete failed, data restored', 0, $exception);
        }

        return $this;
    }
}
