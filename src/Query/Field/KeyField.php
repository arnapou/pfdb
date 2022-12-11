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

namespace Arnapou\PFDB\Query\Field;

/**
 * This field returns
 * - value : the key itself
 * - select : a custom field with the provided name.
 */
class KeyField implements FieldValueInterface, FieldSelectInterface
{
    private readonly string $name;

    public function __construct(?string $name = null)
    {
        $this->name = $name ?? ':key';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(array $row, string|int|null $key = null): string|int|float|bool|null|array
    {
        return $key;
    }

    public function select(array $row, string|int|null $key = null): array
    {
        return [$this->name => $key];
    }
}
