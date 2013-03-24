<?php

namespace PFDB\Exception;

use PFDB\Table;

class Exception extends \Exception {

	/**
	 *
	 * @param string $type 
	 */
	static function throwBadArgumentTypeException($type) {
		throw new BadArgumentTypeException("Argument type error, expected : $type");
	}

	/**
	 *
	 * @param string $directory 
	 */
	static function throwDirectoryNotFoundException($directory) {
		throw new DirectoryNotFoundException("Directory not found '$directory'");
	}

	/**
	 *
	 * @param string $directory 
	 */
	static function throwDirectoryNotWritableException($directory) {
		throw new DirectoryNotWritableException("Directory not writable '$directory'");
	}

	/**
	 *
	 * @param string $name 
	 */
	static function throwInvalidTableNameException($name) {
		throw new InvalidTableNameException("Invalid table name '$name'");
	}

	/**
	 *
	 * @param Table $table 
	 */
	static function throwInvalidTableDataException($table) {
		throw new InvalidTableDataException("Invalid table data (maybe corrupted) '" . $table->getName() . "'");
	}

	/**
	 *
	 * @param string $class 
	 */
	static function throwInvalidTableClassException($class) {
		throw new InvalidTableClassException("Invalid table class '" . $class . "'");
	}

	/**
	 *
	 * @param string $class 
	 */
	static function throwUnknownClassException($class) {
		throw new UnknownClassException("Unknown class '" . $class . "'");
	}

	/**
	 *
	 * @param string $operator 
	 */
	static function throwUnknownOperatorException($operator) {
		throw new UnknownOperatorException("Unknown operator '" . $operator . "'");
	}

	/**
	 *
	 * @param string $message 
	 */
	static function throwInvalidQuerySyntaxException($message) {
		throw new InvalidQuerySyntaxException("Query syntax error : " . $message);
	}

	/**
	 *
	 */
	static function throwInvalidRootOperatorException() {
		throw new InvalidRootOperatorException("Root operator should be either AND or OR.");
	}

	/**
	 *
	 * @param string $key 
	 */
	static function throwArrayKeyNotFoundException($key) {
		throw new ArrayKeyNotFoundException("Array key '$key' not found.");
	}

}
