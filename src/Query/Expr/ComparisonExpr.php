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

use Arnapou\PFDB\Exception\InvalidFilterOperatorException;
use Arnapou\PFDB\Exception\InvalidFilterValueException;

class ComparisonExpr implements ExprInterface
{
    private const OPERATOR_ALIASES = [
        'match' => 'regexp',
        'regex' => 'regexp',
        '~'     => 'regexp',
        '='     => '==',
        '<>'    => '!=',
    ];
    private const OPERATOR_REGEXP = [
        'like',
        'regexp',
    ];
    /**
     * @var mixed
     */
    private $field;
    /**
     * @var string
     */
    private $operator;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var bool
     */
    private $caseSensitive;
    /**
     * @var bool
     */
    private $not;

    public function __construct($field, string $operator, $value, bool $caseSensitive = true)
    {
        $this->field         = $field;
        $this->value         = $value;
        $this->caseSensitive = $caseSensitive;
        $this->not           = false;
        $this->operator      = $this->sanitizeOperator($operator);

        $this->init();
    }

    public function __invoke(array $row): bool
    {
        [$value1, $value2] = $this->values($row);

        if ($this->not) {
            return !$this->evaluate($value1, $value2);
        } else {
            return $this->evaluate($value1, $value2);
        }
    }

    private function evaluate($value1, $value2): bool
    {
        switch ($this->operator) {
            case '==':
                return $value1 == $value2;
            case '===':
                return $value1 === $value2;
            case '!=':
                return $value1 != $value2;
            case '!==':
                return $value1 !== $value2;
            case '>':
                return $value1 > $value2;
            case '>=':
                return $value1 >= $value2;
            case '<':
                return $value1 < $value2;
            case '<=':
                return $value1 <= $value2;
            case '*':
                return strpos($value1, $value2) !== false;
            case '^':
                return strpos($value1, $value2) === 0;
            case '$':
                return substr($value1, -\strlen($value2)) === "$value2";
            case 'in':
                return \in_array($value1, (array)$value2);
            case 'like':
            case 'regexp':
                return preg_match($value2, $value1) ? true : false;
        }
        throw new InvalidFilterOperatorException('Operator = ' . $this->operator);
    }

    private function values(array $row): array
    {
        if (\is_string($this->field)) {
            $value1 = $row[$this->field] ?? null;
        } elseif (\is_object($this->field) && \is_callable($this->field)) {
            $value1 = \call_user_func($this->field, $row);
        } else {
            $value1 = $this->field;
        }

        if (\is_object($this->value) && \is_callable($this->value)) {
            $value2 = \call_user_func($this->value, $row);
        } else {
            $value2 = $this->value;
        }

        if (!$this->caseSensitive && !\in_array($this->operator, self::OPERATOR_REGEXP)) {
            $value1 = strtolower($value1);
            $value2 = strtolower($value2);
        }

        return [$value1, $value2];
    }

    private function init(): void
    {
        if (!\is_string($this->value) && \in_array($this->operator, self::OPERATOR_REGEXP)) {
            throw new InvalidFilterValueException('Value for operator "' . $this->operator . '" should be a string');
        }

        switch ($this->operator) {
            case 'like':
                $this->value = '/^' . preg_quote($this->value) . '$/' . ($this->caseSensitive ? '' : 'i');
                $this->value = str_replace('_', '.', $this->value);
                $this->value = str_replace('%', '.*', $this->value);
                break;
            case 'regexp':
                $char = $this->value[0];
                if (!preg_match('/^' . preg_quote($char) . '.+' . preg_quote($char) . '[imsxeADSUXJu]*$/', $this->value)) {
                    $this->value = '/' . $this->value . '/' . ($this->caseSensitive ? '' : 'i');
                } elseif (!$this->caseSensitive) {
                    $flags = substr($this->value, strrpos($this->value, $this->value[0]) + 1);
                    if (strpos($flags, 'i') !== false) {
                        $this->value .= 'i';
                    }
                }
                break;
            case 'in':
                if (\is_string($this->value)) {
                    $this->operator = '*';
                }
                break;
        }
    }

    private function sanitizeOperator(string $sanitized): string
    {
        $sanitized = strtolower($sanitized);

        if (strpos($sanitized, 'not') !== false) {
            $this->not = true;
            $sanitized = trim(str_replace('not', '', $sanitized));
        }

        if (\array_key_exists($sanitized, self::OPERATOR_ALIASES)) {
            $sanitized = self::OPERATOR_ALIASES[$sanitized];
        }

        return $sanitized;
    }
}
