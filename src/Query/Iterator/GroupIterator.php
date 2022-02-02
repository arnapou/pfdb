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

class GroupIterator implements \IteratorAggregate
{
    private readonly array $fields;
    private readonly \Closure $reduce;
    private ?\Closure $onfinish = null;

    /**
     * GroupIterator constructor.
     */
    public function __construct(
        private readonly \Iterator $iterator,
        array|string $fields,
        private readonly array $initial,
        callable $reduce,
        ?callable $onfinish
    ) {
        $this->fields = (array) $fields;
        $this->reduce = $reduce(...);
        $this->onfinish = $onfinish ? $onfinish(...) : null;
    }

    public function getIterator(): \Traversable
    {
        $grouped = [];
        foreach ($this->iterator as $key => $row) {
            $groupKey = $this->getGroupKey($row, $key);
            $value = \array_key_exists($groupKey, $grouped) ? $grouped[$groupKey] : $this->initial;
            $grouped[$groupKey] = ($this->reduce)($value, $row, $key);
        }
        if ($this->onfinish) {
            $grouped = array_map($this->onfinish, $grouped);
        }

        return new \ArrayIterator(array_values($grouped));
    }

    private function getGroupKey(array $row, string $key): string
    {
        $keys = [];
        foreach ($this->fields as $field) {
            if (\is_callable($field)) {
                $keys[] = $field($row, $key);
            } else {
                $keys[] = $row[$field] ?? '';
            }
        }

        return md5(serialize($keys));
    }
}
