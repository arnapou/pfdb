<?php

namespace PFDB\Query;

class AndQuery implements QueryInterface {

	protected $querys = array();
	protected $operator = 'AND';

	public function __construct($querys = null) {
		if ( null !== $querys ) {
			foreach ( $querys as $query ) {
				$this->add($query);
			}
		}
	}

	public function add(QueryInterface $query) {
		$this->querys[] = $query;
	}

	public function match($key, $value) {
		foreach ( $this->querys as $query ) {
			if ( !$query->match($key, $value) ) {
				return false;
			}
		}
		return true;
	}

	public function toArray() {
		$elements = array();
		foreach ( $this->querys as $query ) {
			$elements[] = $query->toArray();
		}
		return array(
			'operator' => $this->operator,
			'elements' => $elements
		);
	}

}
