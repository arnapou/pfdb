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

namespace Arnapou\PFDB\Tests\Query\Field;

use Arnapou\PFDB\Query\Field\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function testName(): void
    {
        $field = new Field('name');
        self::assertSame('name', $field->name());
    }

    public function testSelect(): void
    {
        $field = new Field('name');

        self::assertSame(
            ['name' => 'Joe'],
            $field->select(['name' => 'Joe', 'age' => 20])
        );

        self::assertSame(
            ['name' => null],
            $field->select(['firstname' => 'Joe', 'age' => 20])
        );
    }
}
