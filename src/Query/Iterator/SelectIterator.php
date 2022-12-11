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

use Arnapou\PFDB\Core\Assert;
use Arnapou\PFDB\Query\Field\FieldSelectInterface;

use function is_callable;

use Iterator;
use Stringable;

/**
 * @template-implements Iterator<array>
 */
class SelectIterator implements Iterator
{
    /**
     * SelectIterator constructor.
     *
     * @param array<FieldSelectInterface|string|Stringable|callable> $fields
     */
    public function __construct(private readonly Iterator $iterator, private readonly array $fields)
    {
    }

    public function current(): array
    {
        $row = Assert::isArray($this->iterator->current());
        $key = $this->iterator->key();
        if (!$this->fields) {
            return Assert::isArray($row);
        }

        $toMerge = [];
        $data = [];
        foreach ($this->fields as $field) {
            if ('*' === $field) {
                $toMerge[] = $row;
            } elseif ($field instanceof FieldSelectInterface) {
                $toMerge[] = $field->select(Assert::isArray($row), Assert::isIntStringNull($key));
            } elseif (is_callable($field)) {
                $toMerge[] = (array) $field($row, $key);
            } else {
                $str = Assert::isString($field);
                $data[$str] = $row[$str] ?? null;
            }
        }

        return array_merge(array_merge(...$toMerge), $data);
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }
}
