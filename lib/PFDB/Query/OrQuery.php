<?php

namespace PFDB\Query;

class OrQuery extends AndQuery {

	protected $operator = 'OR';

	public function match($key, $value) {
		foreach ( $this->querys as $query ) {
			if ( $query->match($key, $value) ) {
				return true;
			}
		}
		return false;
	}

}
