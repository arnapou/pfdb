<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query;

class AndQuery implements QueryInterface {

	protected $queries = array();

	public function __construct($queries = null) {
		if ( null !== $queries ) {
			foreach ( $queries as $query ) {
				$this->add($query);
			}
		}
	}

	public function add(QueryInterface $query) {
		$this->queries[] = $query;
	}

	public function match($key, $value) {
		foreach ( $this->queries as $query ) {
			if ( !$query->match($key, $value) ) {
				return false;
			}
		}
		return true;
	}

}
