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

use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Field\Value;

trait SanitizeHelperTrait
{
    private function sanitizeField(string|int|float|bool|null|array|FieldValueInterface|callable $field): \Closure
    {
        if (\is_string($field)) {
            return [new Field($field), 'value'](...);
        }

        if ($field instanceof FieldValueInterface) {
            return [$field, 'value'](...);
        }

        if (\is_callable($field)) {
            return $field(...);
        }

        return [new Value($field), 'value'](...);
    }

    private function sanitizeValue(
        string|int|float|bool|null|array|FieldValueInterface|callable $value,
        ExprOperator $operator,
        bool $caseSensitive
    ): \Closure {
        if (ExprOperator::IN === $operator && !\is_array($value)) {
            throw new InvalidValueException('Value for operator "' . $operator->value . '" should be an array');
        }

        if (ExprOperator::LIKE === $operator || ExprOperator::NLIKE === $operator) {
            if (!\is_string($value)) {
                throw new InvalidValueException('Value for operator "' . $operator->value . '" should be a string');
            }

            return $this->sanitizeValueLike($value, $caseSensitive);
        }

        if (ExprOperator::MATCH === $operator || ExprOperator::NMATCH === $operator) {
            if (!\is_string($value)) {
                throw new InvalidValueException('Value for operator "' . $operator->value . '" should be a string');
            }

            return $this->sanitizeValueMatch($value, $caseSensitive);
        }

        if ($value instanceof FieldValueInterface) {
            return [$value, 'value'](...);
        }

        if (\is_callable($value)) {
            return $value(...);
        }

        return [new Value($value), 'value'](...);
    }

    private function sanitizeValueLike(string $value, bool $caseSensitive): \Closure
    {
        $value = '/^' . preg_quote($value, '/') . '$/' . ($caseSensitive ? '' : 'i');
        $value = str_replace(['_', '%'], ['.', '.*'], $value);

        return [new Value($value), 'value'](...);
    }

    private function sanitizeValueMatch(string $value, bool $caseSensitive): \Closure
    {
        $delim = '/' === $value[0] ? '\\/' : preg_quote($value[0], '/');
        if (!preg_match('/^' . $delim . '.+' . $delim . '[imsxeADSUXJu]*$/', $value)) {
            $value = '/' . $value . '/' . ($caseSensitive ? '' : 'i');
        } elseif (!$caseSensitive) {
            $flags = substr($value, (int) strrpos($value, $value[0]) + 1);
            if (!str_contains($flags, 'i')) {
                $value .= 'i';
            }
        }

        return [new Value($value), 'value'](...);
    }
}
