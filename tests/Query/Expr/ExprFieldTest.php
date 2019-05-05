<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query\Expr;

use Arnapou\PFDB\Query\Helper\ExprTrait;
use Arnapou\PFDB\Tests\Query\Field\ForeignFieldTest;
use PHPUnit\Framework\TestCase;

class ExprFieldTest extends TestCase
{
    use ExprTrait;

    public function testField()
    {
        $this->assertSame('test', $this->expr()->field('test')->name());
    }

    public function testKeyField()
    {
        $this->assertSame('test', $this->expr()->keyField('test')->name());
    }

    public function testForeignField()
    {
        $this->assertSame('fkid', $this->expr()->foreignField('fkid', ForeignFieldTest::foreignTable(), null)->name());
    }
}
