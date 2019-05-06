<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Expr;

use Arnapou\PFDB\Exception\InvalidExprFieldException;
use Arnapou\PFDB\Exception\InvalidExprOperatorException;
use Arnapou\PFDB\Exception\InvalidExprValueException;
use Arnapou\PFDB\Query\Field\Field;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Field\Value;

class ComparisonExpr implements ExprInterface
{
    /**
     * @var callable
     */
    private $field;
    /**
     * @var string
     */
    private $operator;
    /**
     * @var callable
     */
    private $value;
    /**
     * @var bool
     */
    private $caseSensitive;
    /**
     * @var bool
     */
    private $not = false;

    /**
     * @param string|FieldValueInterface|callable $field
     * @param string                              $operator
     * @param mixed|FieldValueInterface|callable  $value
     * @param bool                                $caseSensitive
     * @throws InvalidExprFieldException
     * @throws InvalidExprValueException
     */
    public function __construct($field, string $operator, $value, bool $caseSensitive = true)
    {
        $this->caseSensitive = $caseSensitive;
        $this->operator      = $this->sanitizeOperator($operator);
        $this->field         = $this->sanitizeField($field);
        $this->value         = $this->sanitizeValue($value);
    }

    public function __invoke(array $row, $key = null): bool
    {
        $field = \call_user_func($this->field, $row, $key);
        $value = \call_user_func($this->value, $row, $key);

        if (!$this->caseSensitive) {
            switch ($this->operator) {
                case 'like':
                case 'regexp':
                    // nothing to do, already managed in sanitizeValue method
                    break;
                case 'in':
                    $field = strtolower($field);
                    $value = array_map('strtolower', (array)$value);
                    break;
                default:
                    $field = strtolower($field);
                    $value = strtolower($value);
            }
        }

        if ($this->not) {
            return !$this->evaluate($field, $value);
        } else {
            return $this->evaluate($field, $value);
        }
    }

    private function evaluate($field, $value): bool
    {
        switch ($this->operator) {
            case '==':
                return $field == $value;
            case '===':
                return $field === $value;
            case '!=':
                return $field != $value;
            case '!==':
                return $field !== $value;
            case '>':
                return $field > $value;
            case '>=':
                return $field >= $value;
            case '<':
                return $field < $value;
            case '<=':
                return $field <= $value;
            case '*':
                return $value !== '' ? strpos($field, $value) !== false : true;
            case '^':
                return $value !== '' ? strpos($field, $value) === 0 : true;
            case '$':
                return substr($field, -\strlen($value)) === "$value";
            case 'in':
                return \in_array($field, (array)$value);
            case 'like':
            case 'regexp':
                return preg_match($value, $field) ? true : false;
        }
        throw new InvalidExprOperatorException('Operator = ' . $this->operator);
    }

    private function sanitizeOperator(string $operator): string
    {
        $sanitized = strtolower($operator);
        if (strpos($sanitized, 'not') !== false) {
            $this->not = true;
            $sanitized = trim(str_replace('not', '', $sanitized));
        }
        $aliases = [
            'match' => 'regexp',
            'regex' => 'regexp',
            '~'     => 'regexp',
            '='     => '==',
            '<>'    => '!=',
        ];
        if (\array_key_exists($sanitized, $aliases)) {
            $sanitized = $aliases[$sanitized];
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
        throw new InvalidExprFieldException();
    }

    private function sanitizeValue($value): callable
    {
        if (\in_array($this->operator, ['like', 'regexp']) && !\is_string($value)) {
            throw new InvalidExprValueException('Value for operator "' . $this->operator . '" should be a string');
        }
        if (\in_array($this->operator, ['in']) && !\is_array($value)) {
            throw new InvalidExprValueException('Value for operator "' . $this->operator . '" should be an array');
        }

        switch ($this->operator) {
            case 'like':
                $value = '/^' . preg_quote($value) . '$/' . ($this->caseSensitive ? '' : 'i');
                $value = str_replace('_', '.', $value);
                $value = str_replace('%', '.*', $value);
                break;
            case 'regexp':
                $char = $value[0] === '/' ? '\\/' : preg_quote($value[0]);
                if (!preg_match('/^' . $char . '.+' . $char . '[imsxeADSUXJu]*$/', $value)) {
                    $value = '/' . $value . '/' . ($this->caseSensitive ? '' : 'i');
                } elseif (!$this->caseSensitive) {
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
        throw new InvalidExprValueException();
    }

    public function getField(): callable
    {
        return $this->field;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): callable
    {
        return $this->value;
    }

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function isNot(): bool
    {
        return $this->not;
    }
}
