<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Helper;

use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Field\KeyField;
use Arnapou\PFDB\Query\Field\ParentField;
use Arnapou\PFDB\Query\Field\Value;
use Arnapou\PFDB\Table;

class FieldsHelper
{
    public function normal(string $name): Field
    {
        return new Field($name);
    }

    public function value(string $value): Value
    {
        return new Value($value);
    }

    public function key(?string $name = null): KeyField
    {
        return new KeyField($name);
    }

    public function parent(string $name, Table $table, $parentField = null, $selectAlias = null): ParentField
    {
        return new ParentField($name, $table, $parentField, $selectAlias);
    }
}
