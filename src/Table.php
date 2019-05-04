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

use Arnapou\PFDB\Exception\PrimaryKeyNotFoundException;
use Arnapou\PFDB\Exception\ReadonlyException;
use Arnapou\PFDB\Exception\ValueNotFoundException;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Expr\ExprTrait;
use Arnapou\PFDB\Query\Query;
use Arnapou\PFDB\Storage\StorageInterface;
use Traversable;

class Table implements \IteratorAggregate
{
    use ExprTrait;

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
    private $data = null;
    /**
     * @var bool
     */
    private $changed = false;
    /**
     * @var string
     */
    private $primaryKey = null;
    /**
     * @var bool
     */
    private $readonly = false;

    public function __construct(string $name, StorageInterface $storage, ?string $primaryKey = null)
    {
        $this->storage    = $storage;
        $this->name       = $name;
        $this->primaryKey = $primaryKey;
        $this->load();
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function setReadonly(bool $readonly): self
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function __destruct()
    {
        if (!$this->readonly && !$this->storage->isReadonly($this->name)) {
            $this->flush();
        }
    }

    public function flush(): bool
    {
        if ($this->changed) {
            if ($this->readonly) {
                throw new ReadonlyException();
            }
            $this->storage->save($this->name, $this->data);
            $this->changed = false;
            return true;
        }
        return false;
    }

    private function load()
    {
        $rows = $this->storage->load($this->name);
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

    public function delete($id): self
    {
        if (!$this->primaryKey) {
            throw new PrimaryKeyNotFoundException();
        }
        if (!\array_key_exists($id, $this->data)) {
            throw new ValueNotFoundException();
        }
        unset($this->data[$id]);
        $this->changed = true;
        return $this;
    }

    public function update(array $value): self
    {
        if (!$this->primaryKey || !\array_key_exists($this->primaryKey, $value)) {
            throw new PrimaryKeyNotFoundException();
        }
        $id = $value[$this->primaryKey];
        if (!\array_key_exists($id, $this->data)) {
            throw new ValueNotFoundException();
        }
        $this->data[$id] = $value;
        $this->changed   = true;
        return $this;
    }

    public function insert(array $value): self
    {
        if (!$this->primaryKey) {
            throw new PrimaryKeyNotFoundException();
        }
        $maxId = \array_key_exists($this->primaryKey, $value)
            ? $value[$this->primaryKey]
            : max(\count($this->data) - 1, ...array_keys($this->data));

        $this->data[$maxId + 1] = $value;
        $this->changed          = true;
        return $this;
    }

    public function upsert(array $value): self
    {
        if (!$this->primaryKey) {
            throw new PrimaryKeyNotFoundException();
        }
        if (\array_key_exists($this->primaryKey, $value)) {
            return $this->update($value);
        } else {
            return $this->insert($value);
        }
    }

    public function get($id): ?array
    {
        if (!$this->primaryKey) {
            throw new PrimaryKeyNotFoundException();
        }
        return $this->data[$id] ?? null;
    }

    public function find(ExprInterface...$exprs): Query
    {
        $query = new Query($this);
        return $query->where(...$exprs);
    }

    public function getIterator(): Traversable
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

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
}
