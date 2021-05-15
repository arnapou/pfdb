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
 * This is an expression with nested expressions.
 */
interface NestedExprInterface extends ExprInterface
{
    /**
     * Return the children.
     *
     * @return ExprInterface[]
     */
    public function children(): array;

    /**
     * Remove all children.
     */
    public function clear(): void;

    /**
     * Whether the expression is empty (no children).
     */
    public function isEmpty(): bool;

    /**
     * Add a child.
     */
    public function add(ExprInterface $expr): self;
}
