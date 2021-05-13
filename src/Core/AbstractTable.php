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

abstract class AbstractTable implements \IteratorAggregate, TableInterface
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    /**
     * @var string
     */
    private $name;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var bool
     */
    private $changed = false;
    /**
     * @var ?string
     */
    private $primaryKey = null;
    /**
     * @var bool
     */
    private $readonly = false;
    /**
     * @var mixed
     */
    private $lastInsertedKey = null;
    /**
     * @var callable
     */
    private $primaryKeyGenerator;

    final public function __construct(StorageInterface $storage, string $name, ?string $primaryKey)
    {
        $this->storage = $storage;
        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->primaryKeyGenerator = function (): int {
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
        $this->load($this->storage->load($this->name));
    }

    public function setPrimaryKeyGenerator(callable $callable): self
    {
        $this->primaryKeyGenerator = $callable;

        return $this;
    }

    public function getLastInsertedKey()
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

    public function __destruct()
    {
        if (!$this->isReadonly()) {
            $this->flush();
        }
    }

    public function flush(): bool
    {
        if ($this->changed) {
            if ($this->isReadonly()) {
                throw new ReadonlyException();
            }
            $this->storage->save($this->name, $this->primaryKey ? array_values($this->data) : $this->data);
            $this->changed = false;

            return true;
        }

        return false;
    }

    public function clear(): self
    {
        $this->data = [];

        return $this;
    }

    protected function load(array $rows): void
    {
        if ($this->primaryKey) {
            $this->data = [];
            foreach ($rows as $row) {
                if (!\array_key_exists($this->primaryKey, $row)) {
                    throw new PrimaryKeyNotFoundException();
                }
                $this->data[$row[$this->primaryKey]] = $row;
            }
        } else {
            $this->data = $rows;
        }
        $this->changed = false;
    }

    public function find(ExprInterface ...$exprs): Query
    {
        $query = new Query($this);

        return $query->where(...$exprs);
    }

    public function get($id): ?array
    {
        return $this->data[$id] ?? null;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param int|string|null $key
     *
     * @return int|string
     */
    protected function detectKey(array $value, $key = null)
    {
        if (null === $key) {
            if (!$this->primaryKey || !\array_key_exists($this->primaryKey, $value)) {
                throw new PrimaryKeyNotFoundException();
            }

            return $value[$this->primaryKey];
        }

        return $key;
    }

    public function delete($id): self
    {
        if (null === $id || !\array_key_exists($id, $this->data)) {
            throw new ValueNotFoundException();
        }
        unset($this->data[$id]);
        $this->changed = true;

        return $this;
    }

    public function update(array $value, $key = null): self
    {
        $key = $this->detectKey($value, $key);
        if (!\array_key_exists($key, $this->data)) {
            throw new ValueNotFoundException();
        }
        $this->data[$key] = array_merge($this->data[$key], $value);
        $this->changed = true;

        return $this;
    }

    public function insert(array $value, $key = null): self
    {
        try {
            $key = $this->detectKey($value, $key);
            if (\array_key_exists($key, $this->data)) {
                throw new PrimaryKeyAlreadyExistsException();
            }
        } catch (PrimaryKeyNotFoundException $e) {
            $key = \call_user_func($this->primaryKeyGenerator);
        }
        if ($this->primaryKey) {
            $value[$this->primaryKey] = $key;
        }

        $this->data[$key] = $value;
        $this->changed = true;
        $this->lastInsertedKey = $key;

        return $this;
    }

    public function upsert(array $value, $key = null): self
    {
        try {
            $key = $this->detectKey($value, $key);
        } catch (PrimaryKeyNotFoundException $e) {
            $key = null;
        }

        if (null !== $key && \array_key_exists($key, $this->data)) {
            return $this->update($value, $key);
        }

        return $this->insert($value, $key);
    }

    public function insertMultiple(array $rows): self
    {
        $bkupData = $this->data;
        $bkupChanged = $this->changed;
        try {
            foreach ($rows as $row) {
                $this->insert($row);
            }
        } catch (\Throwable $exception) {
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
                    $this->data[$key] = $function($row, $key);
                }
            }
        } catch (\Throwable $exception) {
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
        } catch (\Throwable $exception) {
            $this->data = $bkupData;
            $this->changed = $bkupChanged;
            throw new MultipleActionException('Multiple delete failed, data restored', 0, $exception);
        }

        return $this;
    }
}
