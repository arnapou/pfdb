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
use Arnapou\PFDB\Query\Expr\NestedExprInterface;
use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Helper\FieldsHelperTrait;
use Arnapou\PFDB\Query\Iterator\GroupIterator;
use Arnapou\PFDB\Query\Iterator\SelectIterator;
use Arnapou\PFDB\Query\Iterator\SortIterator;

class Query implements \IteratorAggregate, \Countable
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    /**
     * @var array|callable
     */
    private $select = [];
    /**
     * @var \Iterator
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
    private $sorts = [];

    public function __construct(?\Traversable $from = null)
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
     * @param array $fields
     * @return Query
     */
    public function select(...$fields): self
    {
        if (empty($fields)) {
            $this->select = [];
        } else {
            $this->select = $fields;
        }
        return $this;
    }

    public function addSelect(...$fields): self
    {
        foreach ($fields as $field) {
            $this->select[] = $field;
        }
        return $this;
    }

    public function from(\Traversable $iterator): self
    {
        if ($iterator instanceof \Iterator) {
            $this->from = $iterator;
        } else {
            $this->from = new \IteratorIterator($iterator);
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

    public function sort(...$sorts): self
    {
        $this->sorts = $sorts;
        return $this;
    }

    public function addSort($field, string $order = 'ASC'): self
    {
        if (!\is_scalar($field) && \is_callable($field)) {
            $this->sorts[] = $field;
        } else {
            $this->sorts[] = [$field, strtoupper($order ?: 'ASC')];
        }
        return $this;
    }

    public function getIterator(): \Traversable
    {
        $iterator = $this->where->isEmpty()
            ? $this->from
            : new \CallbackFilterIterator($this->from, $this->where);
        if ($this->group) {
            $iterator = new \IteratorIterator(new GroupIterator($iterator, ...$this->group));
        }
        if ($this->sorts) {
            $iterator = new \IteratorIterator(new SortIterator($iterator, $this->sorts));
        }
        if ($this->limit !== [0, PHP_INT_MAX]) {
            $iterator = new \LimitIterator(
                $iterator instanceof \SeekableIterator ? new \IteratorIterator($iterator) : $iterator,
                $this->limit[0],
                $this->limit[1]
            );
        }
        if ($this->select) {
            $iterator = new SelectIterator($iterator, $this->select);
        }
        return $iterator;
    }

    public function chain(bool $cut = true): self
    {
        if ($cut) {
            return new self(new \ArrayIterator(iterator_to_array($this)));
        } else {
            return new self($this);
        }
    }

    public function first()
    {
        $first = null;
        foreach ($this as $item) {
            $first = $item;
            break;
        }
        return $first;
    }

    public function last()
    {
        $last = null;
        foreach ($this as $item) {
            $last = $item;
        }
        return $last;
    }

    public function count(): int
    {
        return iterator_count($this);
    }
}
