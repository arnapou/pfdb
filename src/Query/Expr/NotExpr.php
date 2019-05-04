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

class NotExpr implements ExprInterface
{
    /**
     * @var ExprInterface
     */
    private $expr;

    public function __construct(ExprInterface $expr)
    {
        $this->expr = $expr;
    }

    public function __invoke(array $row): bool
    {
        return !$this->expr->__invoke($row);
    }
}
