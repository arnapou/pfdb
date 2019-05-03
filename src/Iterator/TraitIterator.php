<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Iterator;

use Arnapou\PFDB\Condition\ConditionBuilder;
use Arnapou\PFDB\Condition\ConditionInterface;
use Arnapou\PFDB\Exception\Exception;
use Iterator;

trait TraitIterator
{
    /**
     *
     * @param array $array
     * @return bool
     */
    protected function isAssociativeArray($array)
    {
        $values = array_values($array);
        $diff   = array_diff_key($values, $array);
        return empty($diff) ? false : true;
    }

    /**
     * Find rows which match the condition.
     *
     * The condition can be either :
     * - ConditionInterface object
     * - ConditionBuilder object
     * - Array (uses ConditionBuilder::fromArray)
     * - single key
     *
     * @param mixed $condition
     */
    public function find($condition): ConditionIterator
    {
        if ($condition instanceof ConditionInterface) {
            return new ConditionIterator($this, $condition);
        } elseif ($condition instanceof ConditionBuilder) {
            return new ConditionIterator($this, $condition->getCondition());
        } elseif (\is_array($condition) && $this->isAssociativeArray($condition)) {
            $builder = ConditionBuilder::AND();
            foreach ($condition as $key => $value) {
                if (!\is_int($key)) {
                    $builder->equalTo($key, $value);
                }
            }
            return new ConditionIterator($this, $builder->getCondition());
        } else {
            return new ConditionIterator($this, ConditionBuilder::AND()->equalTo(null, $condition)->getCondition());
        }
    }

    /**
     * Sorts data by columns.
     *
     * Be warned that it means iterate over all data before sorting.
     *
     * It you manipulate huge data, this simple method can take time and memory.
     *
     * @param array $orders        Example : array('field1' => true, 'field2' => false)
     *                             - true : ascending
     *                             - false : descending
     * @param bool  $caseSensitive true by default
     * @return Iterator
     */
    public function sort($orders, $caseSensitive = true)
    {
        if (!\is_array($orders)) {
            Exception::throwBadArgumentTypeException('array');
        }

        // column indexes
        $columnIndexes = array_flip(array_keys($orders));

        // sort orders
        $sortOrders = [];
        foreach ($orders as $column => $asc) {
            $asc == strtolower($asc);
            if ($asc == 'asc') {
                $asc = SORT_ASC;
            } elseif ($asc == 'desc') {
                $asc = SORT_DESC;
            } else {
                $asc = $asc ? SORT_ASC : SORT_DESC;
            }
            $sortOrders[] = $asc;
        }

        // data by column
        $dataByColumn = [];
        $allData      = [];
        $keys         = [];
        foreach ($this as $key => $row) {
            $allData[$key] = $row;
            $keys[]        = $key;
            foreach ($orders as $column => $asc) {
                $value = null;
                if (isset($row[$column])) {
                    $value = $row[$column];
                    if (!$caseSensitive) {
                        $value = strtolower($value);
                    }
                }
                $dataByColumn[$columnIndexes[$column]][] = $value;
            }
        }

        // sort
        $nbSort = \count($orders);
        $args   = [];
        for ($i = 0; $i < $nbSort; $i++) {
            $args[] = &$dataByColumn[$i];
            $args[] = $sortOrders[$i];
        }
        $args[] = &$keys;
        $args[] = &$allData;
        \call_user_func_array('array_multisort', $args);

        // needed to preserve keys
        $allData = array_combine($keys, $allData);

        return new ConditionIterator(new ArrayIterator($allData));
    }

    /**
     * Limits data like any 'sql like' condition
     *
     * @param int $offset
     * @param int $count
     * @return Iterator
     */
    public function limit($offset = 0, $count = null)
    {
        if ($offset < 0 || $count < 0) {
            $nbItems = $iterator->count();
        }
        if ($offset < 0) {
            $offset = $nbItems + $offset;
        }
        if ($count < 0) {
            $count = $nbItems + $count - $offset + 1;
        } elseif ($count === null) {
            $count = -1;
        }
        return new ConditionIterator(new \LimitIterator($this, $offset, $count));
    }
}
