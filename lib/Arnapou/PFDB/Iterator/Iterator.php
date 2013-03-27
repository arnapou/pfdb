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

class Iterator extends \FilterIterator implements \Countable {

	/**
	 *
	 * @var ConditionInterface
	 */
	protected $condition = null;

	/**
	 *
	 * @param \Iterator $iterator
	 * @param ConditionInterface $condition 
	 */
	public function __construct($iterator, $condition = null) {
		if ( !($iterator instanceof \Iterator) ) {
			Exception::throwInvalidConditionSyntaxException("iterator is not a valid php iterator");
		}
		if ( $condition !== null && !($condition instanceof ConditionInterface) ) {
			Exception::throwInvalidConditionSyntaxException("condition is not a valid Arnapou\PFDB\Condition\ConditionInterface");
		}
		$this->condition = $condition;
		parent::__construct($iterator);
	}

	public function accept() {
		if ( $this->condition === null ) {
			return true;
		}
		else {
			return $this->condition->match($this->key(), $this->current());
		}
	}

	public function count() {
		return iterator_count($this);
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
			return new self($this, $condition);
		}
		elseif ( $condition instanceof ConditionBuilder ) {
			return new self($this, $condition->getCondition());
		}
		elseif ( is_array($condition) ) {
			return new self($this, ConditionBuilder::fromArray($condition)->getCondition());
		}
		else {
			return new self($this, ConditionBuilder::createAnd()->equalTo(null, $condition)->getCondition());
		}
		return new self(new \EmptyIterator());
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

		return new self(new \ArrayIterator($allData));
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
			$nbItems = $this->count();
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
		$limitIterator = new \LimitIterator($this, $offset, $count);
		return new self($limitIterator);
	}

}
