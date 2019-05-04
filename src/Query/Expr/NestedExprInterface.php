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

interface NestedExprInterface extends ExprInterface
{
    /**
     * @return ExprInterface[]
     */
    public function children(): iterable;

    public function clear(): void;

    public function add(ExprInterface $expr): self;
}
