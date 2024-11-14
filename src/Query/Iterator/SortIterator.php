<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Iterator;

use Arnapou\Ensure\Enforce;
use ArrayIterator;
use Closure;
use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * @phpstan-type _Sorts array<callable|string|array{string, string}>
 *
 * @template-implements IteratorAggregate<array>
 */
class SortIterator implements IteratorAggregate
{
    /** @var array<callable> */
    private readonly array $sorts;

    /**
     * @param _Sorts $sorts
     */
    public function __construct(private readonly Iterator $iterator, array $sorts)
    {
        $this->sorts = $this->sanitizeOrderings($sorts);
    }

    /**
     * @return Traversable<array-key, mixed>
     */
    public function getIterator(): Traversable
    {
        $rows = iterator_to_array($this->iterator);
        uasort(
            $rows,
            function (mixed $row1, mixed $row2): int {
                if (!\is_array($row1) || !\is_array($row2)) {
                    return 0;
                }

                foreach ($this->sorts as $callable) {
                    if (0 !== ($result = Enforce::int($callable($row1, $row2)))) {
                        return $result;
                    }
                }

                return 0;
            },
        );

        return new ArrayIterator($rows);
    }

    /**
     * @param _Sorts $sorts
     *
     * @return array<callable>
     */
    private function sanitizeOrderings(array $sorts): array
    {
        $sanitized = [];
        foreach ($sorts as $sort) {
            $sort = (array) $sort;
            $field = $sort[0];
            if (\is_callable($field)) {
                $sanitized[] = $field;
            } else {
                $way = $sort[1] ?? 'ASC';
                $sanitized[] = $this->createCallable(Enforce::string($field), Enforce::string('' === $way ? 'ASC' : $way));
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
