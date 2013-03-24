<?php

namespace PFDB\Query\Operator;

class GreaterThanOrEqualOperator extends AbstractOperator {

	protected $operator = '>=';

	public function match($key, $value) {
		$testedValue = $this->getTestedValue($key, $value);
		if ( $testedValue === null ) {
			return false;
		}
		return $testedValue >= $this->value;
	}

}
