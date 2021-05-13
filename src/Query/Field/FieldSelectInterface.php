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

interface FieldSelectInterface
{
    /**
     * @param scalar|null $key
     *
     * @return array<string, ?mixed>
     */
    public function select(array $row, $key = null): array;
}
