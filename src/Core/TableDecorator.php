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

use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Query;
use IteratorAggregate;
use Traversable;

/**
 * Utility abstract class to facilitate the creation of custom Table based on others.
 *
 * @template-implements IteratorAggregate<int|string, array>
 */
class TableDecorator implements IteratorAggregate, TableInterface
{
    public function __construct(protected TableInterface $table)
    {
    }

    public function count(): int
    {
        return $this->table->count();
    }

    public function isReadonly(): bool
    {
        return $this->table->isReadonly();
    }

    public function setReadonly(bool $readonly): self
    {
        $this->table->setReadonly($readonly);

        return $this;
    }

    public function find(ExprInterface ...$exprs): Query
    {
        return $this->table->find(...$exprs);
    }

    public function get(int|string $key): ?array
    {
        return $this->table->get($key);
    }

    public function getName(): string
    {
        return $this->table->getName();
    }

    public function getPrimaryKey(): ?string
    {
        return $this->table->getPrimaryKey();
    }

    public function getData(): array
    {
        return $this->table->getData();
    }

    public function delete(int|string|null $key): self
    {
        $this->table->delete($key);

        return $this;
    }

    public function getLastInsertedKey(): string|int|null
    {
        return $this->table->getLastInsertedKey();
    }

    public function update(array $row, int|string|null $key = null): self
    {
        $this->table->update($row, $key);

        return $this;
    }

    public function insert(array $row, int|string|null $key = null): self
    {
        $this->table->insert($row, $key);

        return $this;
    }

    public function upsert(array $row, int|string|null $key = null): self
    {
        $this->table->upsert($row, $key);

        return $this;
    }

    public function insertMultiple(array $rows): self
    {
        $this->table->insertMultiple($rows);

        return $this;
    }

    public function updateMultiple(ExprInterface $expr, callable $function): self
    {
        $this->table->updateMultiple($expr, $function);

        return $this;
    }

    public function deleteMultiple(ExprInterface $expr): self
    {
        $this->table->deleteMultiple($expr);

        return $this;
    }

    public function flush(): bool
    {
        return $this->table->flush();
    }

    public function clear(): self
    {
        $this->table->clear();

        return $this;
    }

    /**
     * @return Traversable<int|string, array<mixed>>
     */
    public function getIterator(): Traversable
    {
        return $this->table;
    }
}
