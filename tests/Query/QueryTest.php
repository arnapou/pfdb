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

namespace Arnapou\PFDB\Tests\Query;

use Arnapou\PFDB\Query\Helper\ExprHelperTrait;
use Arnapou\PFDB\Query\Query;
use Arnapou\PFDB\Table;
use Arnapou\PFDB\Tests\DatabaseTest;
use Arnapou\PFDB\Tests\Storage\PhpFileStorageTest;
use ArrayIterator;
use PHPUnit\Framework\TestCase;

use function strval;

class QueryTest extends TestCase
{
    use ExprHelperTrait;

    /**
     * @var Table[]
     */
    protected $tables = [];

    protected function table(?string $pk = null): Table
    {
        if (!isset($this->table["_$pk"])) {
            $this->tables["_$pk"] = DatabaseTest::pfdbDatabase()->getTable('vehicle', $pk);
        }

        return $this->tables["_$pk"];
    }

    public function testCount()
    {
        self::assertCount(9, $this->table());
        self::assertCount(2, $this->table()->find($this->expr()->eq('color', 'Brown')));
    }

    public function testLimit()
    {
        self::assertCount(1, $this->table()->find()->limit(0, 1));
    }

    public function testFirst()
    {
        self::assertSame(
            [
                'id' => 5,
                'mark' => 'Peugeot',
                'color' => 'Red',
                'price' => '1550',
            ],
            $this->table()->find()->first()
        );
    }

    public function testLast()
    {
        self::assertSame(
            [
                'id' => 89,
                'mark' => 'Nissan',
                'color' => 'Red',
                'price' => '1500',
            ],
            $this->table()->find()->last()
        );
    }

    public function testGet()
    {
        self::assertSame(
            ['id' => 67, 'mark' => 'Nissan', 'color' => 'Brown', 'price' => '1700'],
            $this->table('id')->get(67)
        );
    }

    public function testSelect()
    {
        self::assertSame(
            [5, 14, 22, 31, 45, 52, 67, 71, 89],
            array_keys(iterator_to_array($this->table('id')->find()->select('id')))
        );
        self::assertSame(
            [['id' => 5], ['id' => 14], ['id' => 22], ['id' => 31], ['id' => 45], ['id' => 52], ['id' => 67], ['id' => 71], ['id' => 89]],
            array_values(iterator_to_array($this->table()->find()->select('id')))
        );
        self::assertSame(
            [['ID' => '50'], ['ID' => '140'], ['ID' => '220'], ['ID' => '310'], ['ID' => '450'], ['ID' => '520'], ['ID' => '670'], ['ID' => '710'], ['ID' => '890']],
            array_values(
                iterator_to_array(
                    $this->table()->find()->select(
                        function ($row) {
                            return ['ID' => strval(10 * $row['id'])];
                        }
                    )
                )
            )
        );
        self::assertSame(
            [
                ['ID' => '50', 'mark' => 'Peugeot'],
                ['ID' => '140', 'mark' => 'Peugeot'],
                ['ID' => '220', 'mark' => 'Peugeot'],
                ['ID' => '310', 'mark' => 'Citroen'],
                ['ID' => '450', 'mark' => 'Citroen'],
                ['ID' => '520', 'mark' => 'Citroen'],
                ['ID' => '670', 'mark' => 'Nissan'],
                ['ID' => '710', 'mark' => 'Nissan'],
                ['ID' => '890', 'mark' => 'Nissan'],
            ],
            array_values(
                iterator_to_array(
                    $this->table()->find()->select(
                        function ($row) {
                            return ['ID' => strval(10 * $row['id'])];
                        }
                    )->addSelect('mark')
                )
            )
        );
    }

    public function testSorts()
    {
        self::assertSame(
            [['id' => 14], ['id' => 22], ['id' => 52], ['id' => 89], ['id' => 5], ['id' => 67], ['id' => 71], ['id' => 45], ['id' => 31]],
            array_values(iterator_to_array($this->table()->find()->select('id')->addSort('price')))
        );
        self::assertSame(
            [['id' => 31], ['id' => 45], ['id' => 71], ['id' => 67], ['id' => 5], ['id' => 89], ['id' => 22], ['id' => 52], ['id' => 14]],
            array_values(iterator_to_array($this->table()->find()->select('id')->addSort('price', 'DESC')))
        );
        self::assertSame(
            [['id' => 31], ['id' => 45], ['id' => 71], ['id' => 67], ['id' => 5], ['id' => 89], ['id' => 22], ['id' => 52], ['id' => 14]],
            array_values(
                iterator_to_array(
                    $this->table()->find()->select('id')->addSort(
                        function ($row1, $row2) {
                            return -($row1['price'] <=> $row2['price']);
                        }
                    )
                )
            )
        );
        self::assertSame(
            [['id' => 31], ['id' => 45], ['id' => 71], ['id' => 67], ['id' => 5], ['id' => 89], ['id' => 52], ['id' => 22], ['id' => 14]],
            array_values(iterator_to_array($this->table()->find()->select('id')->addSort('price', 'DESC')->addSort('mark')))
        );
    }

    public function testGroup()
    {
        self::assertSame(
            [['sum' => 4150, 'count' => 3], ['sum' => 5200, 'count' => 3], ['sum' => 4950, 'count' => 3]],
            iterator_to_array(
                $this->table()->find()->group(
                    ['mark'],
                    ['sum' => 0, 'count' => 0],
                    function ($group, $row) {
                        $group['sum'] += $row['price'];
                        ++$group['count'];

                        return $group;
                    }
                )
            )
        );
        self::assertSame(
            [['avg' => 1383.3], ['avg' => 1733.3], ['avg' => 1650.0]],
            iterator_to_array(
                $this->table()->find()->group(
                    ['mark'],
                    ['sum' => 0, 'count' => 0],
                    function ($group, $row) {
                        $group['sum'] += $row['price'];
                        ++$group['count'];

                        return $group;
                    },
                    function ($group) {
                        return ['avg' => round($group['sum'] / $group['count'], 1)];
                    }
                )
            )
        );
        self::assertSame(
            [['ids' => [5, 31, 45, 67, 71, 89]], ['ids' => [14, 22, 52]]],
            iterator_to_array(
                $this->table()->find()->group(
                    [
                        function ($row) {
                            return $row['price'] < 1500 ? 'A' : 'B';
                        },
                    ],
                    ['ids' => []],
                    function ($group, $row) {
                        $group['ids'][] = $row['id'];

                        return $group;
                    }
                )
            )
        );
    }

    public function testForcedChaining()
    {
        $filtered = $this->table()->find($this->expr()->lte('price', 1500));
        $sorted = (new Query($filtered))->sort('mark');
        $limited = (new Query($sorted))->limit(0, 2);
        $final = (new Query($limited))->select('id');

        self::assertSame(
            [52 => ['id' => 52], 89 => ['id' => 89]],
            iterator_to_array($final)
        );
    }

    public function testImplementedChaining()
    {
        $final = $this->table()->find($this->expr()->lte('price', 1500))
            ->chain()->addSort('mark')
            ->chain()->limit(0, 2)
            ->chain()->select('id');

        self::assertSame(
            [52 => ['id' => 52], 89 => ['id' => 89]],
            iterator_to_array($final)
        );
    }

    public function testFromMethodIdenticalAsConstructor()
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('color');

        $query1 = new Query(new ArrayIterator($data));
        $query2 = (new Query())->from(new ArrayIterator($data));

        self::assertSame(iterator_to_array($query1), iterator_to_array($query2));
    }

    public function testStandarSelectFrom()
    {
        $data = PhpFileStorageTest::pfdbStorage()->load('vehicle');
        $query = new Query(new ArrayIterator($data));

        self::assertSame(
            [
                4 => [
                    'id' => 45,
                    'mark' => 'Citroen',
                    'color' => 'Yellow',
                    'price' => '1800',
                ],
            ],
            iterator_to_array(
                $query->select()->where(
                    $this->expr()->and(
                        $this->expr()->eq('color', 'Yellow', false)
                    )
                )
            )
        );
    }
}
