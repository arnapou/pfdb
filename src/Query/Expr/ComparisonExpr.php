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
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Helper\ExprHelper;
use Arnapou\PFDB\Query\Helper\SanitizeHelperTrait;

class ComparisonExpr implements ExprInterface
{
    use SanitizeHelperTrait;

    private bool $not = false;
    /**
     * @var callable
     */
    private $field;
    /**
     * @var callable
     */
    private $value;

    public function __construct(
        string | int | float | bool | null | FieldValueInterface | callable $field,
        private string $operator,
        string | int | float | bool | null | array | FieldValueInterface | callable $value,
        private bool $caseSensitive = true
    ) {
        $this->operator = $this->sanitizeOperator($operator, $this->not);
        $this->field = $this->sanitizeField($field);
        $this->value = $this->sanitizeValue($value, $this->operator, $this->caseSensitive);
    }

    public function __invoke(array $row, null | int | string $key = null): bool
    {
        $field = \call_user_func($this->field, $row, $key);
        $value = \call_user_func($this->value, $row, $key);

        if (null !== $field && !is_scalar($field)) {
            throw new InvalidFieldException('The field value is not a scalar.');
        }

        if (\in_array($this->operator, [ExprHelper::IN, ExprHelper::NIN], true)) {
            if (!\is_array($value)) {
                if (!is_scalar($value)) {
                    throw new InvalidValueException('Value for operator "' . $this->operator . '" should be an array');
                }
                $value = (array) $value;
            }

            if (!$this->caseSensitive) {
                $field = strtolower((string) $field);
                $value = array_map('strtolower', $value);
            }

            $bool = match ($this->operator) {
                ExprHelper::IN => \in_array($field, $value),
                ExprHelper::NIN => !\in_array($field, $value),
            };
        } else {
            if (\is_array($value)) {
                throw new InvalidValueException('Value for operator "' . $this->operator . '" should NOT be an array');
            }

            if (!$this->caseSensitive && !\in_array($this->operator, [ExprHelper::LIKE, ExprHelper::NLIKE, ExprHelper::MATCH, ExprHelper::NMATCH], true)) {
                $field = strtolower((string) $field);
                $value = strtolower((string) $value);
            }

            $bool = match ($this->operator) {
                ExprHelper::EQ => $field == $value,
                ExprHelper::EQSTRICT => $field === $value,
                ExprHelper::NEQ => $field != $value,
                ExprHelper::NEQSTRICT => $field !== $value,
                ExprHelper::GT => $field > $value,
                ExprHelper::GTE => $field >= $value,
                ExprHelper::LT => $field < $value,
                ExprHelper::LTE => $field <= $value,
                ExprHelper::CONTAINS => '' === $value || str_contains((string) $field, (string) $value),
                ExprHelper::BEGINS => '' === $value || str_starts_with((string) $field, (string) $value),
                ExprHelper::ENDS => '' === $value || str_ends_with((string) $field, (string) $value),
                ExprHelper::LIKE, ExprHelper::MATCH => (bool) preg_match((string) $value, (string) $field),
                ExprHelper::NLIKE, ExprHelper::NMATCH => !(bool) preg_match((string) $value, (string) $field),
                default => false,
            };
        }

        return $this->not ? !$bool : $bool;
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
