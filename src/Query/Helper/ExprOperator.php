<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Helper;

use Arnapou\PFDB\Exception\InvalidOperatorException;

enum ExprOperator: string
{
    case EQ = '==';
    case EQSTRICT = '===';
    case NEQ = '!=';
    case NEQSTRICT = '!==';
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case LIKE = 'like';
    case NLIKE = 'not like';
    case MATCH = 'regexp';
    case NMATCH = 'not regexp';
    case ENDS = '$';
    case BEGINS = '^';
    case CONTAINS = '*';
    case IN = 'in';
    case NIN = 'not in';

    /**
     * Warranty to return a valid ExprOperator either it is a string or a valid enum.
     */
    public static function sanitize(self|string $operator, bool &$not = false): self
    {
        if ($operator instanceof self) {
            return $operator;
        }

        [$operator, $not] = self::sanitizeString($operator);

        if (null !== ($enum = self::tryFrom($operator))) {
            return $enum;
        }

        return self::fromAliases($operator);
    }

    /**
     * Mapping for string aliases.
     */
    private static function fromAliases(string $operator): self
    {
        return match ($operator) {
            'match', '~', 'regex' => self::MATCH,
            '=' => self::EQ,
            '<>' => self::NEQ,
            default => throw new InvalidOperatorException("Unknown operator '$operator'"),
        };
    }

    /**
     * @return array{string, bool}
     */
    private static function sanitizeString(string $operator): array
    {
        $operator = strtolower($operator);

        if (str_starts_with($operator, '!')) {
            return [trim(substr($operator, 1)), true];
        }

        if (str_starts_with($operator, 'not')) {
            return [trim(substr($operator, 3)), true];
        }

        return [$operator, false];
    }
}
