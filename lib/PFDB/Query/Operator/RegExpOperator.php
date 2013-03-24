<?php

namespace PFDB\Query\Operator;

class RegExpOperator extends AbstractOperator {

	protected $operator = 'REGEXP';
	protected $regexp;

	public function __construct($field, $value, $caseSensitive = true) {
		parent::__construct($field, $value, $caseSensitive);
		$this->regexp = '/' . $value . '/s';
		if ( !$caseSensitive ) {
			$this->regexp.= 'i';
		}
	}

	public function match($key, $value) {
		$testedValue = $this->getTestedValue($key, $value);
		if ( $testedValue === null ) {
			return false;
		}
		return preg_match($this->regexp, $testedValue);
	}

}
