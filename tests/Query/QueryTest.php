<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Query;

use Arnapou\PFDB\Query\Expr\ExprTrait;
use Arnapou\PFDB\Query\Query;
use Arnapou\PFDB\Storage\PhpFileStorage;
use Arnapou\PFDB\Table;
use Arnapou\PFDB\Tests\TestCase;

class QueryTest extends TestCase
{
    use ExprTrait;

    /**
     * @var Table[]
     */
    protected $tables = [];

    protected function table(?string $pk = null): Table
    {
        if (!isset($this->table["_$pk"])) {
            $storage              = new PhpFileStorage(__DIR__ . '/../../demo/database');
            $this->tables["_$pk"] = new Table('vehicle', $storage, $pk);
        }
        return $this->tables["_$pk"];
    }

    public function testCount()
    {
        $this->assertCount(9, $this->table());
        $this->assertCount(2, $this->table()->find($this->expr()->eq('color', 'Brown')));
    }

    public function testLimit()
    {
        $this->assertCount(1, $this->table()->find()->limit(0, 1));
    }

    public function testSelect()
    {
        $this->assertSame(
            [5, 14, 22, 31, 45, 52, 67, 71, 89],
            array_keys(iterator_to_array($this->table('id')->find()->select('id')))
        );
        $this->assertSame(
            [['id' => 5], ['id' => 14], ['id' => 22], ['id' => 31], ['id' => 45], ['id' => 52], ['id' => 67], ['id' => 71], ['id' => 89]],
            iterator_to_array($this->table()->find()->select('id'))
        );
        $this->assertSame(
            [['ID' => '50'], ['ID' => '140'], ['ID' => '220'], ['ID' => '310'], ['ID' => '450'], ['ID' => '520'], ['ID' => '670'], ['ID' => '710'], ['ID' => '890']],
            iterator_to_array($this->table()->find()->select(function ($row) {
                return ['ID' => \strval(10 * $row['id'])];
            }))
        );
        $this->assertSame(
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
            iterator_to_array($this->table()->find()->select(function ($row) {
                return ['ID' => \strval(10 * $row['id'])];
            })->addSelect('mark'))
        );
    }

    public function testOrderBy()
    {
        $this->assertSame(
            [['id' => 14], ['id' => 22], ['id' => 52], ['id' => 89], ['id' => 5], ['id' => 67], ['id' => 71], ['id' => 45], ['id' => 31]],
            iterator_to_array($this->table()->find()->select('id')->addOrderBy('price'))
        );
        $this->assertSame(
            [['id' => 31], ['id' => 45], ['id' => 71], ['id' => 67], ['id' => 5], ['id' => 89], ['id' => 22], ['id' => 52], ['id' => 14]],
            iterator_to_array($this->table()->find()->select('id')->addOrderBy('price', 'DESC'))
        );
        $this->assertSame(
            [['id' => 31], ['id' => 45], ['id' => 71], ['id' => 67], ['id' => 5], ['id' => 89], ['id' => 52], ['id' => 22], ['id' => 14]],
            iterator_to_array($this->table()->find()->select('id')->addOrderBy('price', 'DESC')->addOrderBy('mark'))
        );
    }

    public function testGroup()
    {
        $this->assertSame(
            [['sum' => 4150, 'count' => 3], ['sum' => 5200, 'count' => 3], ['sum' => 4950, 'count' => 3]],
            iterator_to_array($this->table()->find()->group(
                ['mark'],
                ['sum' => 0, 'count' => 0],
                function ($group, $row) {
                    $group['sum'] += $row['price'];
                    $group['count']++;
                    return $group;
                }
            ))
        );
        $this->assertSame(
            [['avg' => 1383.3], ['avg' => 1733.3], ['avg' => 1650.0]],
            iterator_to_array($this->table()->find()->group(
                ['mark'],
                ['sum' => 0, 'count' => 0],
                function ($group, $row) {
                    $group['sum'] += $row['price'];
                    $group['count']++;
                    return $group;
                },
                function ($group) {
                    return ['avg' => round($group['sum'] / $group['count'], 1)];
                }
            ))
        );
        $this->assertSame(
            [['ids' => [5, 31, 45, 67, 71, 89]], ['ids' => [14, 22, 52]]],
            iterator_to_array($this->table()->find()->group(
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
            ))
        );
    }

    public function testForcedChaining()
    {
        $filtered = $this->table()->find($this->expr()->lte('price', 1500));
        $sorted   = (new Query($filtered))->addOrderBy('mark');
        $limited  = (new Query($sorted))->limit(0, 2);
        $final    = (new Query($limited))->select('id');

        $this->assertSame(
            [['id' => 52], ['id' => 89]],
            iterator_to_array($final)
        );
    }
}
