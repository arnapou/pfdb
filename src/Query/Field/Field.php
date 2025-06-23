<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Field;

/**
 * The simpliest field mapping which returns
 * - value : the $row[$field_name]
 * - select : a simple array [ $field_name => $value ].
 */
class Field implements FieldValueInterface, FieldSelectInterface
{
    public function __construct(private readonly string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(array $row, string|int|null $key = null): string|int|float|bool|array|null
    {
        return $row[$this->name] ?? null;
    }

    public function select(array $row, string|int|null $key = null): array
    {
        return [$this->name => $row[$this->name] ?? null];
    }
}
