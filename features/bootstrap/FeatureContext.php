<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\Behat\BehatTool;
use Arnapou\PFDB\Core\DatabaseInterface;
use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\DatabaseReadonly;
use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Field\KeyField;
use Arnapou\PFDB\Storage\PhpFileStorage;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private BehatTool $tool;
    private DatabaseInterface $db;
    private TableInterface $current;

    public function __construct()
    {
        $this->tool = new BehatTool();
        $this->db = new DatabaseReadonly(new PhpFileStorage(__DIR__ . '/../../tests/database'));
    }

    /**
     * @Given the content of table :name is
     */
    public function theContentOfTableIs(string $name, TableNode $table): void
    {
        $this->match(
            ($this->current = $this->db->getTable($name))->find(),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field1 > :value1 is
     */
    public function querying(string $name, string $field1, int $value1, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->gt($field1, $value1),
                ),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field1 > :value1 AND :field2 = :value2 is
     */
    public function queryingAnd(string $name, string $field1, int $value1, string $field2, string $value2, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->gt($field1, $value1),
                    $this->db->expr()->eq($field2, $value2),
                ),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field1 > :value1 OR :field2 = :value2 is
     */
    public function queryingOr(string $name, string $field1, int $value1, string $field2, string $value2, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->or(
                        $this->db->expr()->gt($field1, $value1),
                        $this->db->expr()->eq($field2, $value2),
                    ),
                ),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field1 > :value1 OR :field2 = :value2 SORT BY :sort1 asc then :sort2 :way2 is
     */
    public function queryingOrSorted(string $name, string $field1, int $value1, string $field2, string $value2, string $sort1, string $sort2, string $way2, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->or(
                        $this->db->expr()->gt($field1, $value1),
                        $this->db->expr()->eq($field2, $value2),
                    ),
                )
                ->sort($sort1, [$sort2, $way2]),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field1 > :value1 OR :field2 = :value2 SORT BY :sort1 asc then :sort2 :way2 LIMIT :offset, :count is
     */
    public function queryingOrSortedLimit(string $name, string $field1, int $value1, string $field2, string $value2, string $sort1, string $sort2, string $way2, int $offset, int $count, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->or(
                        $this->db->expr()->gt($field1, $value1),
                        $this->db->expr()->eq($field2, $value2),
                    ),
                )
                ->sort($sort1, [$sort2, $way2])
                ->limit($offset, $count),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field1 > :value1 OR :field2 = :value2 SORT BY :sort1 asc then :sort2 :way2 LIMIT :offset, :count ==> find :field3 match :regex SORT BY :sort3 asc is
     */
    public function queryingOrSortedLimitChained(string $name, string $field1, int $value1, string $field2, string $value2, string $sort1, string $sort2, string $way2, int $offset, int $count, string $field3, string $regex, string $sort3, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->or(
                        $this->db->expr()->gt($field1, $value1),
                        $this->db->expr()->eq($field2, $value2),
                    ),
                )
                ->sort($sort1, [$sort2, $way2])
                ->limit($offset, $count)
                ->chain()
                ->where(
                    $this->db->expr()->match($field3, $regex),
                )
                ->sort($sort3),
            $table,
        );
    }

    /**
     * @Given the find into :name of (:field1 > :value1 AND :field2 = :value2) OR (:field3 < :value3 AND :field4 = :value4) is
     */
    public function queryingCombined(string $name, string $field1, int $value1, string $field2, string $value2, string $field3, int $value3, string $field4, string $value4, TableNode $table): void
    {
        $this->match(
            $this->db->getTable($name)
                ->find(
                    $this->db->expr()->or(
                        $this->db->expr()->and(
                            $this->db->expr()->gt($field1, $value1),
                            $this->db->expr()->eq($field2, $value2),
                        ),
                        $this->db->expr()->and(
                            $this->db->expr()->lt($field3, $value3),
                            $this->db->expr()->eq($field4, $value4),
                        ),
                    ),
                ),
            $table,
        );
    }

    /**
     * @Given the find into :name of :field IN (:stringIds) SORT BY :sort asc is
     */
    public function queryingIn(string $name, string $field, string $stringIds, string $sort, TableNode $table): void
    {
        $ids = array_map(intval(...), explode(',', $stringIds));
        $this->match(
            $this->db->getTable($name)
                ->find($this->db->expr()->in($field, $ids))
                ->sort($sort),
            $table,
        );
    }

    /**
     * @Given the find into :name of {key} IN (:stringIds) SORT BY :sort asc is
     */
    public function queryingInKey(string $name, string $stringIds, string $sort, TableNode $table): void
    {
        $ids = array_map(intval(...), explode(',', $stringIds));
        $this->match(
            $this->db->getTable($name)
                ->find($this->db->expr()->in(new KeyField(), $ids))
                ->sort($sort),
            $table,
        );
    }

    /**
     * @Given the update of :field1 > :value1 => :field2 / :value2 gives
     */
    public function updateMultiple(string $field1, int $value1, string $field2, int $value2, TableNode $table): void
    {
        $this->match(
            $this->current->updateMultiple(
                $this->db->expr()->gt($field1, $value1),
                function ($row) use ($field2, $value2) {
                    $row[$field2] /= $value2;

                    return $row;
                },
            ),
            $table,
        );
    }

    /**
     * @Then we delete :field1 < :value1 OR :field2 = :value2
     */
    public function deleteMultiple(string $field1, int $value1, string $field2, string $value2, TableNode $table): void
    {
        $this->match(
            $this->current->deleteMultiple(
                $this->db->expr()->or(
                    $this->db->expr()->lt($field1, $value1),
                    $this->db->expr()->eq($field2, $value2, false),
                ),
            ),
            $table,
        );
    }

    /**
     * @Then we update the :field with :value for 'id' = :id
     */
    public function updateById(string $field, int $value, int $id, TableNode $table): void
    {
        $this->match(
            $this->current->update(
                [
                    'id' => $id,
                    $field => $value,
                ],
            ),
            $table,
        );
    }

    /**
     * @Then we update the :field with :value for {key} = :id
     */
    public function updateByKey(string $field, int $value, int $id, TableNode $table): void
    {
        $this->match(
            $this->current->update(
                [$field => $value],
                $id,
            ),
            $table,
        );
    }

    /**
     * @Then we insert the row :rowAsJson
     */
    public function insert(string $rowAsJson, TableNode $table): void
    {
        $this->match(
            $this->current->insert(
                $this->tool->jsonDecode($rowAsJson),
            ),
            $table,
        );
    }

    /**
     * @Then we upsert the row :rowAsJson
     */
    public function upsert(string $rowAsJson, TableNode $table): void
    {
        $this->match(
            $this->current->upsert(
                $this->tool->jsonDecode($rowAsJson),
            ),
            $table,
        );
    }

    /**
     * @Then we upsert the last inserted row with :rowAsJson
     */
    public function upsertLast(string $rowAsJson, TableNode $table): void
    {
        $this->match(
            $this->current->upsert(
                $this->tool->jsonDecode($rowAsJson),
                $this->current->getLastInsertedKey(),
            ),
            $table,
        );
    }

    /**
     * @Then we single delete the {key} :id
     */
    public function deleteSingle(int $id, TableNode $table): void
    {
        $this->match(
            $this->current->delete($id),
            $table,
        );
    }

    /**
     * @Given all 'vehicle_linked' with corresponding names gives
     */
    public function foreign1(TableNode $table): void
    {
        $colors = $this->db->getTable('color');
        $marks = $this->db->getTable('mark');
        $vehicles = $this->db->getTable('vehicle_linked');

        $this->match(
            $vehicles->find()
                ->select(
                    'id',
                    $vehicles->fields()->parent(
                        'color_id',  // field name
                        $colors,     // foreign table
                        'name',      // foreign name
                    ),
                    $vehicles->fields()->parent(
                        'mark_id',
                        $marks,
                    ),
                    'price',
                ),
            $table,
        );
    }

    /**
     * @Given the 'vehicle_linked' filtered on color name :value without displaying it gives
     */
    public function foreign2(string $value, TableNode $table): void
    {
        $colors = $this->db->getTable('color');
        $vehicles = $this->db->getTable('vehicle_linked');

        $this->match(
            $vehicles->find(
                $vehicles->expr()->eq(
                    $vehicles->fields()->parent(
                        'color_id',  // field name
                        $colors,     // foreign table
                        'name',      // foreign name
                    ),
                    $value,
                ),
            ),
            $table,
        );
    }

    /**
     * @Given the 'vehicle_linked' with multiple filters (color = :color AND mark matches :mark) gives
     */
    public function foreign3(string $color, string $mark, TableNode $table): void
    {
        $colors = $this->db->getTable('color');
        $marks = $this->db->getTable('mark');
        $vehicles = $this->db->getTable('vehicle_linked');

        $this->match(
            $vehicles->find(
                $vehicles->expr()->eq(
                    $vehicles->fields()->parent(
                        'color_id',  // field name
                        $colors,     // foreign table
                        'name',      // foreign name
                    ),
                    $color,
                ),
                $vehicles->expr()->contains(
                    $vehicles->fields()->parent(
                        'mark_id',   // field name
                        $marks,      // foreign table
                        'name',      // foreign name
                    ),
                    $mark,
                ),
            ),
            $table,
        );
    }

    private function match(iterable $rows, TableNode $table, ExprInterface ...$conditions): void
    {
        $this->tool->matchTable($table, iterator_to_array($rows, preserve_keys: false));
    }
}
