<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Helper;

trait ExprHelperTrait
{
    private ?ExprHelper $pfdbExprHelper = null;

    public function expr(): ExprHelper
    {
        return $this->pfdbExprHelper ??= new ExprHelper();
    }
}
