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

use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Query;

abstract class AbstractTableAdapter implements TableInterface
{
    /**
     * @var Table
     */
    protected $table;

    public function __construct(TableInterface $table)
    {
        $this->table = $table;
    }

    public function count()
    {
        return $this->table->count();
    }

    public function isReadonly(): bool
    {
        return $this->table->isReadonly();
    }

    public function setReadonly(bool $readonly)
    {
        $this->table->setReadonly($readonly);
        return $this;
    }

    public function find(ExprInterface ...$exprs): Query
    {
        return $this->table->find(...$exprs);
    }

    public function get($id): ?array
    {
        return $this->table->get($id);
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

    public function delete($id)
    {
        $this->table->delete($id);
        return $this;
    }

    public function getLastInsertedKey()
    {
        return $this->table->getLastInsertedKey();
    }

    public function update(array $value, $key = null)
    {
        $this->table->update($value, $key);
        return $this;
    }

    public function insert(array $value, $key = null)
    {
        $this->table->insert($value, $key);
        return $this;
    }

    public function upsert(array $value, $key = null)
    {
        $this->table->upsert($value, $key);
        return $this;
    }

    public function insertMultiple(array $rows)
    {
        $this->table->insertMultiple($rows);
        return $this;
    }

    public function updateMultiple(ExprInterface $expr, callable $function)
    {
        $this->table->updateMultiple($expr, $function);
        return $this;
    }

    public function deleteMultiple(ExprInterface $expr)
    {
        $this->table->deleteMultiple($expr);
        return $this;
    }

    public function flush(): bool
    {
        return $this->table->flush();
    }

    public function clear()
    {
        $this->table->clear();
        return $this;
    }
}
