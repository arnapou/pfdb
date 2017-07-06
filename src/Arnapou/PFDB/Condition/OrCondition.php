<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Condition;

class OrCondition extends AndCondition
{

    public function match($key, $value)
    {
        foreach ($this->conditions as $condition) {
            if ($condition->match($key, $value)) {
                return true;
            }
        }
        return false;
    }

}
