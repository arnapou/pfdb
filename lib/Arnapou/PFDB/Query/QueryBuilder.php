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

use Arnapou\PFDB\Exception\Exception;

class QueryBuilder {

	/**
	 *
	 * @var AndQuery
	 */
	protected $query;

	/**
	 * Instanciate a QueryBuilder
	 *
	 * @param string $operator either 'AND' or 'OR'
	 */
	public function __construct($operator) {
		if ( 'AND' == $operator ) {
			$this->query = new AndQuery();
		}
		elseif ( 'OR' == $operator ) {
			$this->query = new OrQuery();
		}
		else {
			Exception::throwInvalidRootOperatorException();
		}
	}

	/**
	 * Create a QueryBuilder which make AND operations between children
	 *
	 * @return QueryBuilder 
	 */
	static public function createAnd() {
		return new self('AND');
	}

	/**
	 * Create a QueryBuilder which make OR operations between children
	 *
	 * @return QueryBuilder 
	 */
	static public function createOr() {
		return new self('OR');
	}

	/**
	 * Get the final Query object
	 *
	 * @return QueryInterface
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * Add a child query
	 *
	 * @param QueryInterface $query
	 * @return QueryBuilder 
	 */
	public function add($query) {
		if ( $query instanceof QueryBuilder ) {
			$this->query->add($query->getQuery());
		}
		elseif ( $query instanceof QueryInterface ) {
			$this->query->add($query);
		}
		else {
			Exception::throwBadArgumentTypeException('QueryBuilder or QueryInterface');
		}
		return $this;
	}

	/**
	 * Add a greaterThan query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function greaterThan($field, $value, $caseSensitive = true) {
		$this->query->add(new Operator\GreaterThanOperator($field, $value, $caseSensitive));
		return $this;
	}

	/**
	 * Add a greaterThanOrEqual query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function greaterThanOrEqual($field, $value, $caseSensitive = true) {
		$this->query->add(new Operator\GreaterThanOrEqualOperator($field, $value, $caseSensitive));
		return $this;
	}

	/**
	 * Add a lowerThan query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function lowerThan($field, $value, $caseSensitive = true) {
		$this->query->add(new Operator\LowerThanOperator($field, $value, $caseSensitive));
		return $this;
	}

	/**
	 * Add a lowerThanOrEqual query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function lowerThanOrEqual($field, $value, $caseSensitive = true) {
		$this->query->add(new Operator\LowerThanOrEqualOperator($field, $value, $caseSensitive));
		return $this;
	}

	/**
	 * Add a equalTo query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function equalTo($field, $value, $caseSensitive = true) {
		$this->query->add(new Operator\EqualOperator($field, $value, $caseSensitive));
		return $this;
	}

	/**
	 * Add a notEqualTo query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function notEqualTo($field, $value, $caseSensitive = true) {
		$this->query->add(new NotQuery(new Operator\EqualOperator($field, $value, $caseSensitive)));
		return $this;
	}

	/**
	 * Add a in query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function in($field, $values, $caseSensitive = true) {
		$this->query->add(new Operator\InOperator($field, $values, $caseSensitive));
		return $this;
	}

	/**
	 * Add a notIn query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function notIn($field, $values, $caseSensitive = true) {
		$this->query->add(new NotQuery(new Operator\InOperator($field, $values, $caseSensitive)));
		return $this;
	}

	/**
	 * Add a matchRegExp query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function matchRegExp($field, $pattern, $caseSensitive = true) {
		$this->query->add(new Operator\RegExpOperator($field, $pattern, $caseSensitive));
		return $this;
	}

	/**
	 * Add a notMatchRegExp query
	 *
	 * @param string $field use NULL value if you want to query on keys (not field rows)
	 * @param mixed $value
	 * @param bool $caseSensitive (default: true)
	 * @return QueryBuilder 
	 */
	public function notMatchRegExp($field, $pattern, $caseSensitive = true) {
		$this->query->add(new NotQuery(new Operator\RegExpOperator($field, $pattern, $caseSensitive)));
		return $this;
	}

}
