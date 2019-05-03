<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Demo;

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\Condition\ConditionBuilder;
use Arnapou\PFDB\Database;
use Arnapou\PFDB\Storage\PhpFileStorage;
use Arnapou\PFDB\Tests\TestCase;

class VehiclePhpTest extends TestCase
{
    /**
     * @var Database
     */
    protected $database;

    protected function database()
    {
        if (!$this->database) {
            $storage        = new PhpFileStorage(__DIR__ . '/../../demo/database');
            $this->database = new Database($storage);
            $this->database->setAutoFlush(false);
        }
        return $this->database;
    }

    public function testPrice()
    {
        $table = $this->database()->getTable('vehicle');
        $this->assertSame(
            iterator_to_array(
                $table->find(ConditionBuilder::AND()->greaterThan('price', 1500))
            ),
            [
                5  => ['mark' => 'Peugeot', 'color' => 'Red', 'price' => '1550'],
                31 => ['mark' => 'Citroen', 'color' => 'Red', 'price' => '2000'],
                45 => ['mark' => 'Citroen', 'color' => 'Yellow', 'price' => '1800'],
                67 => ['mark' => 'Nissan', 'color' => 'Brown', 'price' => '1700'],
                71 => ['mark' => 'Nissan', 'color' => 'Green', 'price' => '1750'],
            ]
        );
    }

    public function testPriceAndColor()
    {
        $table = $this->database()->getTable('vehicle');
        $this->assertSame(
            iterator_to_array(
                $table->find(ConditionBuilder::AND()->greaterThan('price', 1500)->equalTo('color', 'Red'))
            ),
            [
                5  => ['mark' => 'Peugeot', 'color' => 'Red', 'price' => '1550'],
                31 => ['mark' => 'Citroen', 'color' => 'Red', 'price' => '2000'],
            ]
        );
    }

    public function testPriceOrColor()
    {
        $table = $this->database()->getTable('vehicle');
        $this->assertSame(
            iterator_to_array(
                $table->find(ConditionBuilder::OR()->greaterThan('price', 1500)->equalTo('color', 'Red'))
            ),
            [
                5  => ['mark' => 'Peugeot', 'color' => 'Red', 'price' => '1550'],
                31 => ['mark' => 'Citroen', 'color' => 'Red', 'price' => '2000'],
                45 => ['mark' => 'Citroen', 'color' => 'Yellow', 'price' => '1800'],
                67 => ['mark' => 'Nissan', 'color' => 'Brown', 'price' => '1700'],
                71 => ['mark' => 'Nissan', 'color' => 'Green', 'price' => '1750'],
                89 => ['mark' => 'Nissan', 'color' => 'Red', 'price' => '1500'],
            ]
        );
    }

    public function testFindSortedLimitRegexp()
    {
        $table = $this->database()->getTable('vehicle');
        $this->assertSame(
            iterator_to_array(
                $table
                    ->find(ConditionBuilder::OR()->greaterThan('price', 1500)->equalTo('color', 'Red'))
                    ->sort(['mark' => true, 'price' => false])
                    ->limit(1, 3)
                    ->find(ConditionBuilder::AND()->matchRegExp('color', 'w'))
                    ->sort(['price' => true])
            ),
            [
                67 => ['mark' => 'Nissan', 'color' => 'Brown', 'price' => '1700'],
                45 => ['mark' => 'Citroen', 'color' => 'Yellow', 'price' => '1800'],
            ]
        );
    }
}
