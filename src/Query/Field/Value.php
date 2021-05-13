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
 * The purpose is only to expose a simple value behind a valid interface.
 */
class Value implements FieldValueInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value(array $row, $key = null)
    {
        return $this->value;
    }
}
