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

namespace Arnapou\PFDB\Query\Expr;

/**
 * Simple static boolean expression.
 */
class BoolExpr implements ExprInterface
{
    public function __construct(private readonly bool $bool)
    {
    }

    public function __invoke(array $row, int|string|null $key = null): bool
    {
        return $this->bool;
    }
}
