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

abstract class AbstractNestedExpr implements NestedExprInterface
{
    /**
     * @var ExprInterface[]
     */
    protected $exprs = [];

    public function __construct(ExprInterface ...$exprs)
    {
        $this->exprs = $exprs;
    }

    public function add(ExprInterface $expr): NestedExprInterface
    {
        $this->exprs[] = $expr;

        return $this;
    }

    /**
     * @return ExprInterface[]
     */
    public function children(): array
    {
        return $this->exprs;
    }

    public function clear(): void
    {
        $this->exprs = [];
    }

    public function isEmpty(): bool
    {
        return empty($this->exprs);
    }
}
