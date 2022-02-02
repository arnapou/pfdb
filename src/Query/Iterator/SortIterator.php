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

class SortIterator implements \IteratorAggregate
{
    private readonly array $sorts;

    public function __construct(private readonly \Iterator $iterator, array $sorts)
    {
        $this->sorts = $this->sanitizeOrderings($sorts);
    }

    public function getIterator(): \Traversable
    {
        $rows = iterator_to_array($this->iterator);
        uasort(
            $rows,
            function (array $row1, array $row2): int {
                foreach ($this->sorts as $callable) {
                    if ($result = (int) $callable($row1, $row2)) {
                        return $result;
                    }
                }

                return 0;
            }
        );

        return new \ArrayIterator($rows);
    }

    private function sanitizeOrderings(array $sorts): array
    {
        $sanitized = [];
        foreach ($sorts as $sort) {
            $sort = (array) $sort;
            $field = $sort[0];
            if (\is_callable($field)) {
                $sanitized[] = $field;
            } else {
                $sanitized[] = $this->createCallable($field, ($sort[1] ?? 'ASC') ?: 'ASC');
            }
        }

        return $sanitized;
    }

    private function createCallable(string $field, string $order): \Closure
    {
        if ('ASC' === strtoupper($order)) {
            return static fn (array $r1, array $r2) => ($r1[$field] ?? '') <=> ($r2[$field] ?? '');
        }

        return static fn (array $r1, array $r2) => -(($r1[$field] ?? '') <=> ($r2[$field] ?? ''));
    }
}
