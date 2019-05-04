<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query;

use Arnapou\PFDB\Query\Expr\AndExpr;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Expr\ExprTrait;
use Arnapou\PFDB\Query\Expr\NestedExprInterface;
use Arnapou\PFDB\Query\Iterator\GroupIterator;
use Arnapou\PFDB\Query\Iterator\OrderByIterator;
use Arnapou\PFDB\Query\Iterator\SelectIterator;
use CallbackFilterIterator;
use Iterator;
use IteratorIterator;
use LimitIterator;
use Traversable;

class Query implements \IteratorAggregate
{
    use ExprTrait;

    /**
     * @var array|callable
     */
    private $select = [];
    /**
     * @var Iterator
     */
    private $from;
    /**
     * @var NestedExprInterface
     */
    private $where;
    /**
     * @var array
     */
    private $group = [];
    /**
     * @var array
     */
    private $limit = [0, PHP_INT_MAX];
    /**
     * @var array
     */
    private $orderings = [];

    public function __construct(?Traversable $from)
    {
        $this->where = new AndExpr();
        if ($from) {
            $this->from($from);
        }
    }

    public function where(ExprInterface...$exprs): self
    {
        if (\count($exprs) === 1 && $exprs[0] instanceof NestedExprInterface) {
            $this->where = $exprs[0];
        } else {
            $this->where->clear();
            $this->addWhere(...$exprs);
        }
        return $this;
    }

    public function addWhere(ExprInterface...$exprs): self
    {
        foreach ($exprs as $expr) {
            $this->where->add($expr);
        }
        return $this;
    }

    /**
     * @param null|array|string|callable $fields
     * @return Query
     */
    public function select($fields): self
    {
        if (null === $fields) {
            $this->select = [];
        } elseif (\is_array($fields)) {
            $this->select = $fields;
        } elseif ($fields instanceof Traversable) {
            $this->select = iterator_to_array($fields);
        } else {
            $this->select = [];
            $this->addSelect($fields);
        }
        return $this;
    }

    public function addSelect($field): self
    {
        $this->select[] = $field;
        return $this;
    }

    public function from(\Traversable $iterator): self
    {
        if ($iterator instanceof Iterator) {
            $this->from = $iterator;
        } else {
            $this->from = new IteratorIterator($iterator);
        }
        return $this;
    }

    public function group($fields, array $initial, callable $reduce, ?callable $onfinish = null): self
    {
        $this->group = [$fields, $initial, $reduce, $onfinish];
        return $this;
    }

    public function limit(int $offset = 0, int $count = PHP_INT_MAX): self
    {
        $this->limit = [$offset, $count];
        return $this;
    }

    public function orderBy(array $orderings): self
    {
        $this->orderings[] = $orderings;
        return $this;
    }

    public function addOrderBy($field, string $order = 'ASC'): self
    {
        if (\is_object($field) && \is_callable($field)) {
            $this->orderings[] = [$field, null];
        } else {
            $this->orderings[] = [$field, strtoupper($order ?: 'ASC')];
        }
        return $this;
    }

    public function getIterator(): Traversable
    {
        $iterator = $this->where->isEmpty()
            ? $this->from
            : new CallbackFilterIterator($this->from, $this->where);
        if ($this->group) {
            $iterator = new IteratorIterator(new GroupIterator($iterator, ...$this->group));
        }
        if ($this->orderings) {
            $iterator = new IteratorIterator(new OrderByIterator($iterator, $this->orderings));
        }
        if ($this->limit !== [0, PHP_INT_MAX]) {
            $iterator = new LimitIterator($iterator, $this->limit[0], $this->limit[1]);
        }
        if ($this->select) {
            $iterator = new SelectIterator($iterator, $this->select);
        }
        return $iterator;
    }
}
