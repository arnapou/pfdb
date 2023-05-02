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

use Arnapou\PFDB\Exception\InvalidValueException;
use Arnapou\PFDB\Query\Helper\ExprOperator;
use Arnapou\PFDB\Query\Helper\SanitizeHelperTrait;
use PHPUnit\Framework\TestCase;

class SanitizeHelperTraitTest extends TestCase
{
    public function testSanitizeValueForLikeShouldBeAString(): void
    {
        $obj = new class() {
            use SanitizeHelperTrait {
                sanitizeValue as public;
            }
        };
        $this->expectException(InvalidValueException::class);
        $obj->sanitizeValue(null, ExprOperator::LIKE, true);
    }

    public function testSanitizeValueForMatchShouldBeAString(): void
    {
        $obj = new class() {
            use SanitizeHelperTrait {
                sanitizeValue as public;
            }
        };
        $this->expectException(InvalidValueException::class);
        $obj->sanitizeValue(null, ExprOperator::MATCH, true);
    }
}
