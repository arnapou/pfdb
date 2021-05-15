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

/**
 * Simple static boolean expression.
 */
class BoolExpr implements ExprInterface
{
    private bool $bool;

    public function __construct(bool $bool)
    {
        $this->bool = $bool;
    }

    public function __invoke(array $row, null | int | string $key = null): bool
    {
        return $this->bool;
    }
}
