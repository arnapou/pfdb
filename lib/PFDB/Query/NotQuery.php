<?php

namespace PFDB\Query;

class NotQuery implements QueryInterface {

	protected $query;

	public function __construct(QueryInterface $query) {
		$this->query = $query;
	}

	public function match($key, $value) {
		return!$this->query->match($key, $value);
	}

	public function toArray() {
		return array(
			'operator' => 'NOT',
			'query' => $this->query->toArray(),
		);
	}

}
