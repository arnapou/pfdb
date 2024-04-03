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
use Arnapou\Ensure\Ensure;
use ArrayIterator;
use Closure;
use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<array>
 */
class GroupIterator implements IteratorAggregate
{
    /** @var array<string|callable> */
    private readonly array $fields;
    private readonly Closure $reduce;
    private ?Closure $onfinish = null;

    /**
     * @param array<string|callable>|string $fields
     * @param array<mixed>                  $initial
     */
    public function __construct(
        private readonly Iterator $iterator,
        array|string $fields,
        private readonly array $initial,
        callable $reduce,
        ?callable $onfinish
    ) {
        $this->fields = (array) $fields;
        $this->reduce = $reduce(...);
        $this->onfinish = null === $onfinish ? null : $onfinish(...);
    }

    /**
     * @return Traversable<array-key, mixed>
     */
    public function getIterator(): Traversable
    {
        $grouped = [];
        foreach ($this->iterator as $key => $row) {
            $groupKey = $this->getGroupKey(Ensure::array($row), Enforce::string($key));
            $value = \array_key_exists($groupKey, $grouped) ? $grouped[$groupKey] : $this->initial;
            $grouped[$groupKey] = ($this->reduce)($value, $row, $key);
        }
        if (null !== $this->onfinish) {
            $grouped = array_map($this->onfinish, $grouped);
        }

        return new ArrayIterator(array_values($grouped));
    }

    /**
     * @param array<mixed> $row
     */
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
