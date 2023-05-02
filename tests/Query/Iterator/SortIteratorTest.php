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

namespace Arnapou\PFDB\Tests\Query\Iterator;

use Arnapou\PFDB\Query\Iterator\SortIterator;
use Generator;
use PHPUnit\Framework\TestCase;

class SortIteratorTest extends TestCase
{
    /**
     * This test is for coverage.
     */
    public function testNotArrayValues(): void
    {
        $iterator = static function (): Generator {
            yield 1;
            yield 2;
        };
        $sort = new SortIterator($iterator(), []);
        self::assertSame([1, 2], iterator_to_array($sort));
    }
}
