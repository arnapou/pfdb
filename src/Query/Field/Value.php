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
 * Like a constant value behind the FieldValueInterface.
 */
class Value implements FieldValueInterface
{
    public function __construct(private readonly string|int|float|bool|array|null $value)
    {
    }

    public function value(array $row, string|int|null $key = null): string|int|float|bool|array|null
    {
        return $this->value;
    }
}
