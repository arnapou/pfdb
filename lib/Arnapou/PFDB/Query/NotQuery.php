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

class NotQuery implements QueryInterface {

	protected $query;

	public function __construct(QueryInterface $query) {
		$this->query = $query;
	}

	public function match($key, $value) {
		return!$this->query->match($key, $value);
	}

}
