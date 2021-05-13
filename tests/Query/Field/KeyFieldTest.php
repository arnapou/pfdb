<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query\Field;

use Arnapou\PFDB\Query\Field\KeyField;
use PHPUnit\Framework\TestCase;

class KeyFieldTest extends TestCase
{
    public function test_name()
    {
        $field = new KeyField(':key');
        self::assertSame(':key', $field->name());
    }

    public function test_value()
    {
        $field = new KeyField(':key');

        self::assertSame(
            666,
            $field->value(['name' => 'Joe', 'age' => 20], 666)
        );

        self::assertSame(
            [':key' => 666],
            $field->select(['name' => 'Joe', 'age' => 20], 666)
        );
    }
}
