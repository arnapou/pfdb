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
 * Evaluate the expression with a custom callable.
 *
 * Signature of the callable :
 * <pre>
 * function(array $row, int|string|null $key = null): string|int|float|bool|null|array {
 *     // compute $value
 *     return $value;
 * }
 * </pre>
 */
class FuncExpr implements ExprInterface
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(array $row, null|int|string $key = null): bool
    {
        return (bool) \call_user_func($this->callable, $row, $key);
    }
}
