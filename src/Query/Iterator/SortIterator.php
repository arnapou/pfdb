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
    /**
     * @var array
     */
    private $sorts;
    /**
     * @var \Iterator
     */
    private $iterator;

    public function __construct(\Iterator $iterator, array $sorts)
    {
        $this->iterator = $iterator;
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

    private function createCallable(string $field, string $order): callable
    {
        if ('ASC' === strtoupper($order)) {
            return static function (array $row1, array $row2) use ($field) {
                return ($row1[$field] ?? '') <=> ($row2[$field] ?? '');
            };
        }

        return static function (array $row1, array $row2) use ($field) {
            return -(($row1[$field] ?? '') <=> ($row2[$field] ?? ''));
        };
    }
}
