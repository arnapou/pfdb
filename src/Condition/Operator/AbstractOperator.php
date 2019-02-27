<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Condition\Operator;

use Arnapou\PFDB\Condition\ConditionInterface;
use Arnapou\PFDB\ORM\BaseEntity;

abstract class AbstractOperator implements ConditionInterface
{
    protected $field;
    protected $value;
    protected $caseSensitive;

    public function __construct($field, $value, $caseSensitive = true)
    {
        if ($value instanceof BaseEntity) {
            $value = $value->getId();
        }
        if (!$caseSensitive) {
            if (\is_array($value)) {
                $value = array_map('strtolower', (array)$value);
            } else {
                $value = strtolower($value);
            }
        }
        if (\is_array($value)) {
            $value = array_map('strval', (array)$value);
        } else {
            $value = (string)$value;
        }
        $this->field         = $field;
        $this->value         = $value;
        $this->caseSensitive = $caseSensitive;
    }

    protected function getTestedValue($key, $value)
    {
        if ($this->field === null) {
            $testedValue = $key;
        } elseif (\is_array($value) || $value instanceof \ArrayAccess) {
            if (isset($value[$this->field])) {
                $testedValue = $value[$this->field];
                if ($testedValue instanceof BaseEntity) {
                    $testedValue = $testedValue->getId();
                }
            } else {
                return null;
            }
        } else {
            $testedValue = $value;
        }
        if (!$this->caseSensitive) {
            $testedValue = strtolower($testedValue);
        } else {
            $testedValue = (string)$testedValue;
        }
        return $testedValue;
    }
}
