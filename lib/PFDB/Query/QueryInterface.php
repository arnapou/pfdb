<?php

namespace PFDB\Query;

interface QueryInterface {

	/**
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @return bool
	 */
	public function match($key, $value);

	/**
	 * 
	 * @return array
	 */
	public function toArray();
}
