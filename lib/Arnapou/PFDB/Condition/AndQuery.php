<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Condition;

class AndCondition implements ConditionInterface {

	protected $conditions = array();

	public function __construct($conditions = null) {
		if ( null !== $conditions ) {
			foreach ( $conditions as $condition ) {
				$this->add($condition);
			}
		}
	}

	public function add(ConditionInterface $condition) {
		$this->conditions[] = $condition;
	}

	public function match($key, $value) {
		foreach ( $this->conditions as $condition ) {
			if ( !$condition->match($key, $value) ) {
				return false;
			}
		}
		return true;
	}

}
