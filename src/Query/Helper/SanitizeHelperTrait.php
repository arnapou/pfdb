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

use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Exception\InvalidOperatorException;
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Field\Value;

trait SanitizeHelperTrait
{
    private function sanitizeOperator(string $operator, &$not = false): string
    {
        $sanitized = strtolower($operator);
        if (strpos($sanitized, 'not') !== false) {
            $not       = true;
            $sanitized = trim(str_replace('not', '', $sanitized));
        }
        $aliases       = [
            'match' => 'regexp',
            'regex' => 'regexp',
            '~'     => 'regexp',
            '='     => '==',
            '<>'    => '!=',
        ];
        $sanitized = $aliases[$sanitized] ?? $sanitized;
        if (!\in_array($sanitized, ['==', '===', '!=', '!==', '>', '>=', '<', '<=', '*', '^', '$', 'in', 'like', 'regexp'])) {
            throw new InvalidOperatorException("Unknown operator '$sanitized'");
        }
        return $sanitized;
    }

    private function sanitizeField($field): callable
    {
        if (\is_string($field)) {
            return [new Field($field), 'value'];
        } elseif ($field instanceof FieldValueInterface) {
            return [$field, 'value'];
        } elseif (\is_object($field) && \is_callable($field)) {
            return $field;
        } elseif (is_scalar($field)) {
            return [new Value($field), 'value'];
        }
        throw new InvalidFieldException();
    }

    private function sanitizeValue($value, string $operator, bool $caseSensitive): callable
    {
        if (\in_array($operator, ['like', 'regexp']) && !\is_string($value)) {
            throw new InvalidValueException('Value for operator "' . $operator . '" should be a string');
        }
        if (\in_array($operator, ['in']) && !\is_array($value)) {
            throw new InvalidValueException('Value for operator "' . $operator . '" should be an array');
        }

        switch ($operator) {
            case 'like':
                $value = '/^' . preg_quote($value) . '$/' . ($caseSensitive ? '' : 'i');
                $value = str_replace('_', '.', $value);
                $value = str_replace('%', '.*', $value);
                break;
            case 'regexp':
                $char = $value[0] === '/' ? '\\/' : preg_quote($value[0]);
                if (!preg_match('/^' . $char . '.+' . $char . '[imsxeADSUXJu]*$/', $value)) {
                    $value = '/' . $value . '/' . ($caseSensitive ? '' : 'i');
                } elseif (!$caseSensitive) {
                    $flags = substr($value, strrpos($value, $value[0]) + 1);
                    if (strpos($flags, 'i') === false) {
                        $value .= 'i';
                    }
                }
                break;
        }

        if ($value instanceof FieldValueInterface) {
            return [$value, 'value'];
        } elseif (\is_object($value) && \is_callable($value)) {
            return $value;
        } elseif (\is_scalar($value) || \is_array($value)) {
            return [new Value($value), 'value'];
        }
        throw new InvalidValueException();
    }
}
