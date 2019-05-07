<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query\Helper;

use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Helper\FieldsHelperTrait;
use Arnapou\PFDB\Tests\Query\Field\ParentFieldTest;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    use ExprHelperTrait;
    use FieldsHelperTrait;

    public function test_normal_field()
    {
        $this->assertSame('test', $this->fields()->normal('test')->name());
    }

    public function test_key_field()
    {
        $this->assertSame('test', $this->fields()->key('test')->name());
    }

    public function test_parent_field()
    {
        $this->assertIsCallable($this->fields()->parent('fkid', ParentFieldTest::foreignTable(), null)->name());
    }
}
