<?php

namespace PFDB\Query\Operator;

class InOperator extends AbstractOperator {

	protected $operator = 'IN';

	public function match($key, $value) {
		$testedValue = $this->getTestedValue($key, $value);
		if ( $testedValue === null ) {
			return false;
		}
		return in_array($testedValue, $this->value, true);
	}

}
