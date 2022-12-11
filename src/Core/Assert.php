<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Core;

use Arnapou\PFDB\Exception\Expected;

use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_scalar;
use function is_string;

use Stringable;

final class Assert
{
    /**
     * @return positive-int
     */
    public static function positiveInt(int $value): int
    {
        if ($value >= 1) {
            return $value;
        }
        throw new Expected('positive int', $value);
    }

    public static function isInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        throw new Expected('int', $value);
    }

    public static function isIntStringNull(mixed $value): int|string|null
    {
        if (is_int($value) || is_string($value) || null === $value) {
            return $value;
        }
        throw new Expected('int, string or null', $value);
    }

    public static function isArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        throw new Expected('array', $value);
    }

    public static function isScalar(mixed $value): bool|int|float|string|null
    {
        if (is_bool($value) || is_int($value) || is_float($value) || is_string($value) || null === $value) {
            return $value;
        }
        throw new Expected('scalar', $value);
    }

    public static function isString(mixed $value): string
    {
        if (is_scalar($value)
            || $value instanceof Stringable
            || (is_object($value) && method_exists($value, '__toString'))
        ) {
            return (string) $value;
        }
        throw new Expected('stringable', $value);
    }
}
