<?php

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
 * Represents any value when can get from a row and a key.
 */
interface FieldValueInterface
{
    public function value(array $row, string | int | null $key = null): string | int | float | bool | null | array;
}
