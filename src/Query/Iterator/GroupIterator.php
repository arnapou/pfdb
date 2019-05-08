<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Iterator;

use Iterator;
use Traversable;

class GroupIterator implements \IteratorAggregate
{
    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var array
     */
    private $initial;
    /**
     * @var callable
     */
    private $reduce;
    /**
     * @var callable|null
     */
    private $onfinish;

    public function __construct(Iterator $iterator, $fields, array $initial, callable $reduce, ?callable $onfinish)
    {
        $this->iterator = $iterator;
        $this->fields   = $fields;
        $this->initial  = $initial;
        $this->reduce   = $reduce;
        $this->onfinish = $onfinish;
    }

    public function getIterator(): Traversable
    {
        $grouped = [];
        foreach ($this->iterator as $key => $row) {
            $groupKey           = $this->getGroupKey($row, $key);
            $value              = \array_key_exists($groupKey, $grouped) ? $grouped[$groupKey] : $this->initial;
            $grouped[$groupKey] = \call_user_func($this->reduce, $value, $row, $key);
        }
        if ($this->onfinish) {
            $grouped = array_map($this->onfinish, $grouped);
        }
        return new \ArrayIterator(array_values($grouped));
    }

    private function getGroupKey($row, $key)
    {
        $keys = [];
        foreach ($this->fields as $field) {
            if (!\is_scalar($field) && \is_callable($field)) {
                $keys[] = $field($row, $key);
            } else {
                $keys[] = $row[$field] ?? '';
            }
        }
        return md5(serialize($keys));
    }
}
