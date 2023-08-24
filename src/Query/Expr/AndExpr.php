<?php

declare(strict_types=1);

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
 * Evaluation children expression with AND logic.
 */
class AndExpr extends AbstractNestedExpr
{
    public function __invoke(array $row, int|string $key = null): bool
    {
        foreach ($this->exprs as $expr) {
            if (!$expr->__invoke($row, $key)) {
                return false;
            }
        }

        return true;
    }
}
