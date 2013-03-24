<?php

namespace PFDB\Iterator;

class ArrayIterator implements \Iterator, \Countable {

	protected $array;
	protected $count;
	protected $key;
	protected $current;

	public function __construct(&$array) {
		$this->array = $array;
		$this->count = count($array);
	}

	public function count() {
		return $this->count;
	}

	public function current() {
		return $this->current;
	}

	public function key() {
		return $this->key;
	}

	public function next() {
		next($this->array);
	}

	public function rewind() {
		$this->key = null;
		$this->current = null;
		reset($this->array);
	}

	public function valid() {
		$this->key = key($this->array);
		if ( $this->key === null || $this->key === false ) {
			$this->current = null;
			return false;
		}
		$this->current = current($this->array);
		return true;
	}

}
