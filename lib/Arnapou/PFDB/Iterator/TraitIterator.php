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
use Arnapou\PFDB\ORM\EntityIterator;

trait TraitIterator {

	/**
	 * 
	 * @param array $array
	 * @return bool
	 */
	protected function isAssociativeArray($array) {
		$values = array_values($array);
		$diff = array_diff_key($values, $array);
		return empty($diff) ? false : true;
	}

	/**
	 * 
	 * @param \Iterator $iterator
	 * @param mixed $condition
	 * @return EntityIterator|ConditionIterator
	 */
	protected function getIteratorWrapper($iterator, $condition = null) {
		if ( $this instanceof EntityIterator ) {
			return new EntityIterator($this->getTable(), $iterator, $condition);
		}
		return new ConditionIterator($iterator, $condition);
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
	public function find($condition) {
		if ( $condition instanceof ConditionInterface ) {
			return $this->getIteratorWrapper($this, $condition);
		}
		elseif ( $condition instanceof ConditionBuilder ) {
			return $this->getIteratorWrapper($this, $condition->getCondition());
		}
		elseif ( is_array($condition) && $this->isAssociativeArray($condition) ) {
			$builder = ConditionBuilder::createAnd();
			foreach ( $condition as $key => $value ) {
				if ( !is_int($key) ) {
					$builder->equalTo($key, $value);
				}
			}
			return $this->getIteratorWrapper($this, $builder->getCondition());
		}
		else {
			return $this->getIteratorWrapper($this, ConditionBuilder::createAnd()->equalTo(null, $condition)->getCondition());
		}
	}

	/**
	 * Sorts data by columns.
	 * 
	 * Be warned that it means iterate over all data before sorting.
	 * 
	 * It you manipulate huge data, this simple method can take time and memory.
	 * 
	 * @param array $orders Example : array('field1' => true, 'field2' => false) 
	 *                            - true : ascending
	 *                            - false : descending
	 * @param bool $caseSensitive true by default
	 * @return Iterator
	 */
	public function sort($orders, $caseSensitive = true) {
		if ( !is_array($orders) ) {
			Exception::throwBadArgumentTypeException('array');
		}

		// column indexes
		$columnIndexes = array_flip(array_keys($orders));

		// sort orders
		$sortOrders = array();
		foreach ( $orders as $column => $asc ) {
			$asc == strtolower($asc);
			if ( $asc == 'asc' ) {
				$asc = SORT_ASC;
			}
			elseif ( $asc == 'desc' ) {
				$asc = SORT_DESC;
			}
			else {
				$asc = $asc ? SORT_ASC : SORT_DESC;
			}
			$sortOrders[] = $asc;
		}

		// data by column
		$dataByColumn = array();
		$allData = array();
		$keys = array();
		foreach ( $this as $key => $row ) {
			$allData[$key] = $row;
			$keys[] = $key;
			foreach ( $orders as $column => $asc ) {
				$value = null;
				if ( isset($row[$column]) ) {
					$value = $row[$column];
					if ( !$caseSensitive ) {
						$value = strtolower($value);
					}
				}
				$dataByColumn[$columnIndexes[$column]][] = $value;
			}
		}

		// sort
		$nbSort = count($orders);
		$args = array();
		for ( $i = 0; $i < $nbSort; $i++ ) {
			$args[] = &$dataByColumn[$i];
			$args[] = $sortOrders[$i];
		}
		$args[] = &$keys;
		$args[] = &$allData;
		call_user_func_array('array_multisort', $args);

		// needed to preserve keys
		$allData = array_combine($keys, $allData);

		return $this->getIteratorWrapper(new ArrayIterator($allData));
	}

	/**
	 * Limits data like any 'sql like' condition
	 *
	 * @param int $offset
	 * @param int $count
	 * @return Iterator
	 */
	public function limit($offset = 0, $count = null) {
		if ( $offset < 0 || $count < 0 ) {
			$nbItems = $iterator->count();
		}
		if ( $offset < 0 ) {
			$offset = $nbItems + $offset;
		}
		if ( $count < 0 ) {
			$count = $nbItems + $count - $offset + 1;
		}
		elseif ( $count === null ) {
			$count = -1;
		}
		return $this->getIteratorWrapper(new \LimitIterator($this, $offset, $count));
	}

}
