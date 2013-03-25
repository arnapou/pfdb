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

class OrQuery extends AndQuery {

	public function match($key, $value) {
		foreach ( $this->queries as $query ) {
			if ( $query->match($key, $value) ) {
				return true;
			}
		}
		return false;
	}

}
