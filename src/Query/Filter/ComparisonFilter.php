<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Filter;

use Iterator;

abstract class ComparisonFilter extends \FilterIterator
{
    public function __construct(Iterator $iterator, $left, $right)
    {
        parent::__construct($iterator);
        $this->left = $left;
        $this->right = $right;
    }
}
