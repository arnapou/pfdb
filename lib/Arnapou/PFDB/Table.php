<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB;

use Arnapou\PFDB\Database;
use Arnapou\PFDB\Exception\Exception;
use Arnapou\PFDB\Storage\StorageInterface;

class Table implements \ArrayAccess, \Countable, \IteratorAggregate {

	/**
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 *
	 * @var Database
	 */
	protected $database = null;

	/**
	 *
	 * @var StorageInterface
	 */
	protected $storage = null;

	/**
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 *
	 * @var bool
	 */
	protected $modified = false;

	/**
	 * Instanciate a Table object
	 *
	 * @param Database $database database object
	 * @param string $name should match ^[a-zA-Z0-9_.-]+$
	 */
	public function __construct(Database $database, $name) {
		if ( !preg_match('!^[a-zA-Z0-9_.-]+$!', $name) ) {
			Exception::throwInvalidTableNameException($name);
		}
		$this->database = $database;
		$this->name = $name;
		$this->storage = $database->getStorage();
		$this->reload();
	}

	/**
	 * Reload table data from database storage object
	 *
	 * @return Table 
	 */
	public function reload() {
		$this->modified = false;
		$this->storage->loadTableData($this, $this->data);
		return $this;
	}

	/**
	 * Set a value for a specified key
	 * 
	 * If key is NULL, then it auto-increments the key like php array
	 *
	 * @param mixed $key integer, string or null
	 * @param mixed $value array or simple value
	 * @return mixed
	 */
	public function set($key, $value) {
		if ( $key === null ) {
			$this->data[] = $value;
			end($this->data);
			$key = key($this->data);
		}
		else {
			$this->data[$key] = $value;
		}
		$this->modified = true;
		return $key;
	}

	/**
	 * Get the value for a specified key
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function get($key) {
		if ( isset($this->data[$key]) ) {
			return $this->data[$key];
		}
		return null;
	}

	/**
	 * Build an iterator from a query
	 * 
	 * The query can be either :
	 * - QueryInterface object
	 * - QueryBuilder object
	 * - Array (uses QueryBuilder::fromArray)
	 * - single key
	 *
	 * @param mixed $query
	 * @return Iterator\Iterator 
	 */
	protected function queryToIterator($query) {
		if ( $query instanceof Query\QueryInterface ) {
			return new Iterator\Iterator($this->getIterator(), $query);
		}
		elseif ( $query instanceof Query\QueryBuilder ) {
			return new Iterator\Iterator($this->getIterator(), $query->getQuery());
		}
		elseif ( is_array($query) ) {
			return new Iterator\Iterator($this->getIterator(), Query\QueryBuilder::fromArray($query)->getQuery());
		}
		elseif ( $this->offsetExists($query) ) {
			$results = array($query => $this->get($query));
			return new Iterator\Iterator(new Iterator\ArrayIterator($results));
		}
		return new Iterator\Iterator(new \EmptyIterator());
	}

	/**
	 * Delete rows which match the query
	 * 
	 * The query can be either :
	 * - QueryInterface object
	 * - QueryBuilder object
	 * - Array (uses QueryBuilder::fromArray)
	 * - single key
	 *
	 * @param mixed $query 
	 * @return Table 
	 */
	public function delete($query) {
		$iterator = $this->queryToIterator($query);
		foreach ( $iterator as $key => $row ) {
			$this->offsetUnset($key);
		}
		return $this;
	}

	/**
	 * Update rows which match the query with a valid php callable.
	 * 
	 * The query can be either :
	 * - QueryInterface object
	 * - QueryBuilder object
	 * - Array (uses QueryBuilder::fromArray)
	 * - single key
	 *
	 * @param mixed $query
	 * @param callable $callable Receive one parameter which is the current row. 
	 * 							 The callable should return the updated row.
	 * @return Table 
	 */
	public function update($query, $callable) {
		if ( !is_callable($callable) ) {
			Exception::throwBadArgumentTypeException('callable');
		}
		$iterator = $this->queryToIterator($query);
		foreach ( $iterator as $key => $row ) {
			$this->offsetSet($key, call_user_func($callable, $row));
		}
		return $this;
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
		return $this->queryToIterator($query);
	}

	/**
	 * Find rows which match the query and return the first row if it exists. 
	 * 
	 * It returns NULL if there is no rows to return.
	 * 
	 * The query can be either :
	 * - QueryInterface object
	 * - QueryBuilder object
	 * - Array (uses QueryBuilder::fromArray)
	 * - single key
	 *
	 * @param mixed|array $query
	 * @return mixed
	 */
	public function findOne($query) {
		$results = $this->find($query);
		if ( null !== $results ) {
			foreach ( $results as $result ) {
				return $result;
			}
		}
		return null;
	}

	/**
	 * Return the database of this table
	 *
	 * @return Database
	 */
	public function getDatabase() {
		return $this->database;
	}

	/**
	 * Return the name of this table
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Flush modifications via database storage object
	 *
	 * @return Table 
	 */
	public function flush() {
		if ( $this->isModified() ) {
			$this->storage->storeTableData($this, $this->data);
		}
		return $this;
	}

	/**
	 * Empty the table
	 *
	 * @return Table 
	 */
	public function clear() {
		$this->data = array();
		return $this;
	}

	/**
	 * Drop the table via database storage object
	 *
	 * @return Table 
	 */
	public function drop() {
		$this->clear();
		$this->modified = false;
		$this->storage->destroyTableData($this);
		return $this;
	}

	/**
	 * Tells whether the table is modified or not
	 *
	 * @return bool
	 */
	public function isModified() {
		return $this->modified;
	}

	/**
	 * return the raw data array of this table
	 *
	 * @return array
	 */
	public function getRawData() {
		return $this->data;
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->data[$offset]);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetUnset($offset) {
		$this->modified = true;
		unset($this->data[$offset]);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function count() {
		return count($this->data);
	}

	public function getIterator() {
		return new Iterator\ArrayIterator($this->data);
	}

}
