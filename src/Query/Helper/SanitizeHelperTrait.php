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
    private function sanitizeOperator(string $operator, bool &$not = false): string
    {
        $sanitized = strtolower($operator);
        if (false !== strpos($sanitized, 'not')) {
            $not = true;
            $sanitized = trim(str_replace('not', '', $sanitized));
        }
        $aliases = [
            'match' => 'regexp',
            'regex' => 'regexp',
            '~' => 'regexp',
            '=' => '==',
            '<>' => '!=',
        ];
        $sanitized = $aliases[$sanitized] ?? $sanitized;
        if (!\in_array($sanitized, ['==', '===', '!=', '!==', '>', '>=', '<', '<=', '*', '^', '$', 'in', 'like', 'regexp'])) {
            throw new InvalidOperatorException("Unknown operator '$sanitized'");
        }

        return $sanitized;
    }

    /**
     * @param mixed $field
     *
     * @throws InvalidFieldException
     */
    private function sanitizeField($field): callable
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
        if (is_scalar($field)) {
            return [new Value($field), 'value'];
        }
        throw new InvalidFieldException();
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidValueException
     */
    private function sanitizeValue($value, string $operator, bool $caseSensitive): callable
    {
        if ('in' === $operator && !\is_array($value)) {
            throw new InvalidValueException('Value for operator "' . $operator . '" should be an array');
        }

        if ('like' === $operator) {
            if (!\is_string($value)) {
                throw new InvalidValueException('Value for operator "' . $operator . '" should be a string');
            }

            $value = '/^' . preg_quote($value, '/') . '$/' . ($caseSensitive ? '' : 'i');
            $value = str_replace('_', '.', $value);
            $value = str_replace('%', '.*', $value);

            return [new Value($value), 'value'];
        }

        if ('regexp' === $operator) {
            if (!\is_string($value)) {
                throw new InvalidValueException('Value for operator "' . $operator . '" should be a string');
            }

            $char = '/' === $value[0] ? '\\/' : preg_quote($value[0], '/');
            if (!preg_match('/^' . $char . '.+' . $char . '[imsxeADSUXJu]*$/', $value)) {
                $value = '/' . $value . '/' . ($caseSensitive ? '' : 'i');
            } elseif (!$caseSensitive) {
                $flags = substr($value, (int) strrpos($value, $value[0]) + 1);
                if (false === strpos($flags, 'i')) {
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

        if (\is_scalar($value) || \is_array($value)) {
            return [new Value($value), 'value'];
        }

        throw new InvalidValueException();
    }
}
