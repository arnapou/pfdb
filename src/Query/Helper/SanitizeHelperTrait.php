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

use Arnapou\PFDB\Exception\InvalidOperatorException;
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Field\Value;

trait SanitizeHelperTrait
{
    private function sanitizeOperator(string $operator, bool &$not = false): string
    {
        if (\in_array($operator, ExprHelper::OPERATORS, true)) {
            return $operator;
        }

        $alias = strtolower($operator);
        if (str_starts_with($operator, '!')) {
            $not = true;
            $alias = trim(substr($alias, 1));
        } elseif (str_starts_with($operator, 'not')) {
            $not = true;
            $alias = trim(substr($alias, 3));
        }

        if (\in_array($alias, ExprHelper::OPERATORS, true)) {
            return $operator;
        }

        $aliases = [
            'match' => ExprHelper::MATCH,
            'regex' => ExprHelper::MATCH,
            '~' => ExprHelper::MATCH,
            '=' => ExprHelper::EQ,
            '<>' => ExprHelper::NEQ,
        ];
        if (isset($aliases[$alias])) {
            return $aliases[$alias];
        }

        throw new InvalidOperatorException("Unknown operator '$operator'");
    }

    private function sanitizeField(string | int | float | bool | null | array | FieldValueInterface | callable $field): callable
    {
        if (\is_string($field)) {
            return [new Field($field), 'value'];
        }

        if ($field instanceof FieldValueInterface) {
            return [$field, 'value'];
        }

        if (\is_callable($field)) {
            return $field;
        }

        return [new Value($field), 'value'];
    }

    private function sanitizeValue(string | int | float | bool | null | array | FieldValueInterface | callable $value, string $operator, bool $caseSensitive): callable
    {
        if (ExprHelper::IN === $operator && !\is_array($value)) {
            throw new InvalidValueException('Value for operator "' . $operator . '" should be an array');
        }

        if (ExprHelper::LIKE === $operator || ExprHelper::NLIKE === $operator) {
            if (!\is_string($value)) {
                throw new InvalidValueException('Value for operator "' . $operator . '" should be a string');
            }

            $value = '/^' . preg_quote($value, '/') . '$/' . ($caseSensitive ? '' : 'i');
            $value = str_replace('_', '.', $value);
            $value = str_replace('%', '.*', $value);

            return [new Value($value), 'value'];
        }

        if (ExprHelper::MATCH === $operator || ExprHelper::NMATCH === $operator) {
            if (!\is_string($value)) {
                throw new InvalidValueException('Value for operator "' . $operator . '" should be a string');
            }

            $delim = '/' === $value[0] ? '\\/' : preg_quote($value[0], '/');
            if (!preg_match('/^' . $delim . '.+' . $delim . '[imsxeADSUXJu]*$/', $value)) {
                $value = '/' . $value . '/' . ($caseSensitive ? '' : 'i');
            } elseif (!$caseSensitive) {
                $flags = substr($value, (int) strrpos($value, $value[0]) + 1);
                if (!str_contains($flags, 'i')) {
                    $value .= 'i';
                }
            }

            return [new Value($value), 'value'];
        }

        if ($value instanceof FieldValueInterface) {
            return [$value, 'value'];
        }

        if (\is_callable($value)) {
            return $value;
        }

        return [new Value($value), 'value'];
    }
}
