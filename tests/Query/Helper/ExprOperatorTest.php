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

namespace Arnapou\PFDB\Tests\Query\Helper;

use Arnapou\PFDB\Query\Helper\ExprOperator;
use PHPUnit\Framework\TestCase;

class ExprOperatorTest extends TestCase
{
    public function testHackyNot(): void
    {
        $not = false;
        $operator = ExprOperator::sanitize('!' . ExprOperator::LIKE->value, $not);
        self::assertTrue($not);
        self::assertSame(ExprOperator::LIKE, $operator);
    }
}
