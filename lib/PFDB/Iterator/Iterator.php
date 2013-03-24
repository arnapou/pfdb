<?php

namespace PFDB\Iterator;

use PFDB\Query\QueryBuilder;
use PFDB\Query\QueryInterface;
use PFDB\Exception\Exception;

class Iterator extends \FilterIterator implements \Countable {

	/**
	 *
	 * @var QueryInterface
	 */
	protected $query = null;

	/**
	 *
	 * @param \Iterator $iterator
	 * @param QueryInterface $query 
	 */
	public function __construct($iterator, $query = null) {
		if ( !($iterator instanceof \Iterator) ) {
			Exception::throwInvalidQuerySyntaxException("iterator is not a valid php iterator");
		}
		if ( $query !== null && !($query instanceof QueryInterface) ) {
			Exception::throwInvalidQuerySyntaxException("query is not a valid PFDB\Query\QueryInterface");
		}
		$this->query = $query;
		parent::__construct($iterator);
	}

	public function accept() {
		if ( $this->query === null ) {
			return true;
		}
		else {
			return $this->query->match($this->key(), $this->current());
		}
	}

	public function count() {
		return iterator_count($this);
	}

	/**
	 * Find rows which match the query.
	 * 
	 * The query can be either :
	 * - QueryInterface object
	 * - QueryBuilder object
	 * - Array (uses QueryBuilder::fromArray)
	 * - single key
	 *
	 * @param mixed $query 
	 */
	public function find($query) {
		if ( $query instanceof QueryInterface ) {
			return new self($this, $query);
		}
		elseif ( $query instanceof QueryBuilder ) {
			return new self($this, $query->getQuery());
		}
		elseif ( is_array($query) ) {
			return new self($this, QueryBuilder::fromArray($query)->getQuery());
		}
		else {
			return new self($this, QueryBuilder::createAnd()->equalTo(null, $query)->getQuery());
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
	 * Limits data like any 'sql like' query
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
