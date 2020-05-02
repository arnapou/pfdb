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

use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Exception\InvalidOperatorException;
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Helper\SanitizeHelperTrait;

class ComparisonExpr implements ExprInterface
{
    use SanitizeHelperTrait;

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
     * @throws InvalidFieldException
     * @throws InvalidOperatorException
     * @throws InvalidValueException
     */
    public function __construct($field, string $operator, $value, bool $caseSensitive = true)
    {
        $this->caseSensitive = $caseSensitive;
        $this->operator      = $this->sanitizeOperator($operator, $this->not);
        $this->field         = $this->sanitizeField($field);
        $this->value         = $this->sanitizeValue($value, $this->operator, $this->caseSensitive);
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
        // ------------------------
        // @codeCoverageIgnoreStart
        // -> this code should NEVER happen because the operator is sanitized in the object construction
        trigger_error('Operator not implemented', E_USER_ERROR);
        return false;
        // ------------------------
        // @codeCoverageIgnoreEnd
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
