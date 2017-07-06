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

class NotCondition implements ConditionInterface
{

    protected $condition;

    public function __construct(ConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    public function match($key, $value)
    {
        return !$this->condition->match($key, $value);
    }

}
