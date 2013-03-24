<?php

namespace PFDB\Query\Operator;

use PFDB\Query\QueryInterface;

abstract class AbstractOperator implements QueryInterface {

	protected $field;
	protected $value;
	protected $caseSensitive;
	protected $operator;

	public function __construct($field, $value, $caseSensitive = true) {
		if ( !$caseSensitive ) {
			if ( is_array($value) ) {
				$value = array_map('strtolower', (array) $value);
			}
			else {
				$value = strtolower($value);
			}
		}
		if ( is_array($value) ) {
			$value = array_map('strval', (array) $value);
		}
		else {
			$value = (string) $value;
		}
		$this->field = $field;
		$this->value = $value;
		$this->caseSensitive = $caseSensitive;
	}

	protected function getTestedValue($key, $value) {
		if ( $this->field === null ) {
			$testedValue = $key;
		}
		elseif ( is_array($value) ) {
			if ( isset($value[$this->field]) ) {
				$testedValue = $value[$this->field];
			}
			else {
				return null;
			}
		}
		else {
			$testedValue = $value;
		}
		if ( !$this->caseSensitive ) {
			$testedValue = strtolower($testedValue);
		}
		else {
			$testedValue = (string) $testedValue;
		}
		return $testedValue;
	}

	public function toArray() {
		return array(
			'operator' => $this->operator,
			'field' => $this->field,
			'value' => $this->value,
			'caseSensitive' => $this->caseSensitive,
		);
	}

}
