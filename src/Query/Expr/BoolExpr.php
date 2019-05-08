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

class BoolExpr implements ExprInterface
{
    /**
     * @var bool
     */
    private $bool;

    public function __construct(bool $bool)
    {
        $this->bool = $bool;
    }

    public function __invoke(array $row, $key = null): bool
    {
        return $this->bool;
    }
}
