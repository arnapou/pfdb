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

class OrderByIterator implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $orderings;
    /**
     * @var Iterator
     */
    private $iterator;

    public function __construct(Iterator $iterator, array $orderings)
    {
        $this->iterator  = $iterator;
        $this->orderings = $this->sanitizeOrderings($orderings);
    }

    public function getIterator(): Traversable
    {
        $rows = iterator_to_array($this->iterator);
        usort(
            $rows,
            function (array $row1, array $row2) {
                foreach ($this->orderings as $callable) {
                    if ($result = \intval($callable($row1, $row2))) {
                        return $result;
                    }
                }
                return 0;
            }
        );
        return new \ArrayIterator($rows);
    }

    private function sanitizeOrderings(array $orderings): array
    {
        $sanitized = [];
        foreach ($orderings as $ordering) {
            $field = $ordering[0];
            if (\is_object($field) && \is_callable($field)) {
                $sanitized[] = $field;
            } else {
                $sanitized[] = $this->createCallable($field, $ordering[1] ?: 'ASC');
            }
        }
        return $sanitized;
    }

    private function createCallable($field, $order)
    {
        $way = strtoupper($order) === 'ASC' ? 1 : -1;
        return function (array $row1, array $row2) use ($field, $way) {
            return $way * (($row1[$field] ?? '') <=> ($row2[$field] ?? ''));
        };
    }
}
