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
 * Decorator to invert another expression.
 */
class NotExpr implements ExprInterface
{
    public function __construct(private ExprInterface $expr)
    {
    }

    public function __invoke(array $row, null | int | string $key = null): bool
    {
        return !$this->expr->__invoke($row, $key);
    }
}
