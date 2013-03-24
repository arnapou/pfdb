<?php

namespace PFDB\Query;

use PFDB\Exception\Exception;

class QueryBuilder {

	/**
	 *
	 * @var AndQuery
	 */
	protected $query;

	/**
	 *
	 * @var array
	 */
	protected $operatorClasses = array(
		'IN' => 'PFDB\\Query\\Operator\\InOperator',
		'REGEXP' => 'PFDB\\Query\\Operator\\RegExpOperator',
		'>' => 'PFDB\\Query\\Operator\\GreaterThanOperator',
		'>=' => 'PFDB\\Query\\Operator\\GreaterThanOrEqualOperator',
		'<' => 'PFDB\\Query\\Operator\\LowerThanOperator',
		'<=' => 'PFDB\\Query\\Operator\\LowerThanOrEqualOperator',
		'=' => 'PFDB\\Query\\Operator\\EqualOperator',
	);

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
	 * Create a QueryBuilder from a structured array which represents the query
	 *
	 * @param array $array
	 * @return QueryBuilder 
	 */
	static public function fromArray($array) {
		if ( !is_array($array) ) {
			Exception::throwBadArgumentTypeException('array');
		}
		if ( !isset($array['operator']) ) {
			Exception::throwArrayKeyNotFoundException('operator');
		}
		if ( !isset($array['elements']) ) {
			Exception::throwArrayKeyNotFoundException('elements');
		}
		$operator = $array['operator'];
		$elements = $array['elements'];
		if ( !in_array($operator, array('AND', 'OR')) ) {
			Exception::throwInvalidRootOperatorException();
		}
		$builder = new self($operator);
		foreach ( $elements as $element ) {
			$builder->add(self::arrayToQuery($element));
		}
		return $builder;
	}

	/**
	 * Build a query from an array (used as recursive function to build the whole query)
	 *
	 * @param array $array
	 * @return QueryInterface
	 */
	static protected function arrayToQuery($array) {
		if ( !is_array($array) ) {
			Exception::throwBadArgumentTypeException('array');
		}
		if ( !isset($array['operator']) ) {
			Exception::throwArrayKeyNotFoundException('operator');
		}
		$operator = $array['operator'];
		if ( 'AND' === $operator || 'OR' === $operator ) {
			if ( !isset($array['elements']) ) {
				Exception::throwArrayKeyNotFoundException('elements');
			}
			if ( 'AND' === $operator ) {
				$query = new AndQuery();
			}
			elseif ( 'OR' === $operator ) {
				$query = new OrQuery();
			}
			foreach ( $array['elements'] as $element ) {
				$query->add(self::arrayToQuery($element));
			}
			return $query;
		}
		elseif ( 'NOT' === $operator ) {
			if ( !isset($array['query']) ) {
				Exception::throwArrayKeyNotFoundException('query');
			}
			return new NotQuery(self::arrayToQuery($array['query']));
		}
		else {
			if ( !isset($array['field']) ) {
				Exception::throwArrayKeyNotFoundException('field');
			}
			if ( !isset($array['value']) ) {
				Exception::throwArrayKeyNotFoundException('value');
			}
			if ( !isset($array['caseSensitive']) ) {
				Exception::throwArrayKeyNotFoundException('caseSensitive');
			}
			if ( isset($this->operatorClasses[$operator]) ) {
				$operatorClass = $this->operatorClasses[$operator];
				return new $operatorClass($array['field'], $array['value'], $array['caseSensitive']);
			}
		}
		Exception::throwUnknownOperatorException($operator);
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
