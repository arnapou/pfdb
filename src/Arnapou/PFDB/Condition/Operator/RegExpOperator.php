<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Condition\Operator;

class RegExpOperator extends AbstractOperator {

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
