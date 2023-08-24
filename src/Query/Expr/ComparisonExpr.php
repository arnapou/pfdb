<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Expr;

use Arnapou\Ensure\Enforce;
use Arnapou\PFDB\Exception\InvalidFieldException;
use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Field\FieldValueInterface;
use Arnapou\PFDB\Query\Helper\ExprOperator;
use Arnapou\PFDB\Query\Helper\SanitizeHelperTrait;
use Closure;

use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;

class ComparisonExpr implements ExprInterface
{
    use SanitizeHelperTrait;

    private bool $not = false;
    private readonly ExprOperator $operator;
    private readonly Closure $field;
    private readonly Closure $value;

    public function __construct(
        string|int|float|bool|null|FieldValueInterface|callable $field,
        string|ExprOperator $operator,
        string|int|float|bool|null|array|FieldValueInterface|callable $value,
        private readonly bool $caseSensitive = true
    ) {
        $this->operator = ExprOperator::sanitize($operator, $this->not);
        $this->field = $this->sanitizeField($field);
        $this->value = $this->sanitizeValue($value, $this->operator, $this->caseSensitive);
    }

    public function __invoke(array $row, int|string $key = null): bool
    {
        $field = ($this->field)($row, $key);
        $value = ($this->value)($row, $key);

        if (null !== $field && !is_scalar($field)) {
            throw new InvalidFieldException('The field value is not a scalar.');
        }

        if (in_array($this->operator, [ExprOperator::IN, ExprOperator::NIN], true)) {
            if (!is_array($value) && !is_string($value) && !is_int($value)
                && !is_float($value) && !is_bool($value)) {
                // @codeCoverageIgnoreStart
                // Theoretically not reachable because of sanitizer in constructor.
                throw new InvalidValueException('Value for operator "' . $this->operator->value . '" should be an array');
                // @codeCoverageIgnoreEnd
            }

            $bool = $this->evaluateIn($value, $field);
        } else {
            if (is_array($value)) {
                throw new InvalidValueException('Value for operator "' . $this->operator->value . '" should NOT be an array');
            }

            $bool = $this->evaluateOther($value, $field);
        }

        return $this->not ? !$bool : $bool;
    }

    private function evaluateOther(string|int|float|bool|null $value, mixed $field): bool
    {
        if (!$this->caseSensitive && !in_array(
            $this->operator,
            [ExprOperator::LIKE, ExprOperator::NLIKE, ExprOperator::MATCH, ExprOperator::NMATCH],
            true
        )) {
            $field = strtolower(Enforce::string($field));
            $value = strtolower(Enforce::string($value));
        }

        return match ($this->operator) {
            ExprOperator::EQ => $field == $value,
            ExprOperator::EQSTRICT => $field === $value,
            ExprOperator::NEQ => $field != $value,
            ExprOperator::NEQSTRICT => $field !== $value,
            ExprOperator::GT => $field > $value,
            ExprOperator::GTE => $field >= $value,
            ExprOperator::LT => $field < $value,
            ExprOperator::LTE => $field <= $value,
            ExprOperator::CONTAINS => '' === $value || str_contains(Enforce::string($field), (string) $value),
            ExprOperator::BEGINS => '' === $value || str_starts_with(Enforce::string($field), (string) $value),
            ExprOperator::ENDS => '' === $value || str_ends_with(Enforce::string($field), (string) $value),
            ExprOperator::LIKE, ExprOperator::MATCH => (bool) preg_match(Enforce::nonEmptyString($value), Enforce::string($field)),
            ExprOperator::NLIKE, ExprOperator::NMATCH => !preg_match(Enforce::nonEmptyString($value), Enforce::string($field)),
            default => false,
        };
    }

    private function evaluateIn(string|int|float|bool|array $value, mixed $field): bool
    {
        $value = (array) $value;

        if (!$this->caseSensitive) {
            $field = strtolower(Enforce::string($field));
            $value = array_map('strtolower', $value);
        }

        return match ($this->operator) {
            ExprOperator::IN => in_array($field, $value),
            ExprOperator::NIN => !in_array($field, $value),
            default => false
        };
    }

    public function getField(): Closure
    {
        return $this->field;
    }

    public function getOperator(): ExprOperator
    {
        return $this->operator;
    }

    public function getValue(): Closure
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
