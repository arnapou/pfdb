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
 * Represents any values when can select from a row and a key.
 *
 * This is used for instance to build several fields from one (joins, ...).
 */
interface FieldSelectInterface
{
    /**
     * Return the list of fields we have built.
     *
     * For most cases, this will return a single {field_name => value} array.
     *
     * @param array<string|int|float|bool|array<mixed>|null> $row
     *
     * @return array<mixed>
     */
    public function select(array $row, string|int|null $key = null): array;
}
