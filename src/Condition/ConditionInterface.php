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

interface ConditionInterface
{
    /**
     *
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public function match($key, $value);
}
