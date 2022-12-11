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

use ArrayIterator;
use Closure;

use function is_array;
use function is_callable;

use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<array>
 */
class SortIterator implements IteratorAggregate
{
    private readonly array $sorts;

    public function __construct(private readonly Iterator $iterator, array $sorts)
    {
        $this->sorts = $this->sanitizeOrderings($sorts);
    }

    public function getIterator(): Traversable
    {
        $rows = iterator_to_array($this->iterator);
        uasort(
            $rows,
            function (mixed $row1, mixed $row2): int {
                if (!is_array($row1) || !is_array($row2)) {
                    return 0;
                }

                foreach ($this->sorts as $callable) {
                    if ($result = (int) $callable($row1, $row2)) {
                        return $result;
                    }
                }

                return 0;
            }
        );

        return new ArrayIterator($rows);
    }

    private function sanitizeOrderings(array $sorts): array
    {
        $sanitized = [];
        foreach ($sorts as $sort) {
            $sort = (array) $sort;
            $field = $sort[0];
            if (is_callable($field)) {
                $sanitized[] = $field;
            } else {
                $sanitized[] = $this->createCallable($field, ($sort[1] ?? 'ASC') ?: 'ASC');
            }
        }

        return $sanitized;
    }

    private function createCallable(string $field, string $order): Closure
    {
        if ('ASC' === strtoupper($order)) {
            return static fn (array $r1, array $r2) => ($r1[$field] ?? '') <=> ($r2[$field] ?? '');
        }

        return static fn (array $r1, array $r2) => -(($r1[$field] ?? '') <=> ($r2[$field] ?? ''));
    }
}
