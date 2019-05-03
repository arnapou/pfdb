<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Condition\Operator;

class GreaterThanOrEqualOperator extends AbstractOperator
{
    public function match($key, $value): bool
    {
        $testedValue = $this->getTestedValue($key, $value);
        if ($testedValue === null) {
            return false;
        }
        return $testedValue >= $this->value;
    }
}
