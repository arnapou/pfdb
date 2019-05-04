<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query;

use Arnapou\PFDB\Query\Expr\ExprBuilderTrait;

class Expr
{
    const EQ = '==';
    const NEQ = '!=';
    const GT = '>';
    const GTE = '>=';
    const LT = '<';
    const LTE = '<=';
    const LIKE = 'like';
    const NLIKE = 'not like';
    const MATCH = 'regexp';
    const NMATCH = 'not regexp';
    const ENDS = '$';
    const BEGINS = '^';
    const CONTAINS = '*';
    const IN = 'in';
    const NIN = 'not in';

    use ExprBuilderTrait;
}
