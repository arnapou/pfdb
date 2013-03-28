<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Storage;

use Arnapou\PFDB\Exception\Exception;
use Arnapou\PFDB\Database;
use Arnapou\PFDB\Table;

abstract class AbstractFileStorage implements StorageInterface {

	/**
	 * 
	 * @var string
	 */
	private $storagePath;

	/**
	 *
	 * @var int secondes
	 */
	protected $maxLockDelay = 10;

	/**
	 * 
	 * @param string $storagePath path where the tables are stored
	 * @param bool $autoCreate auto-create folder
	 */
	public function __construct($storagePath) {
		$this->storagePath = rtrim(rtrim($storagePath, '\\'), '/') . DIRECTORY_SEPARATOR;
		$this->checkStorageFolder();
	}

	/**
	 *
	 * @return int secondes
	 */
	public function getMaxLockDelay() {
		return $this->maxLockDelay;
	}

	/**
	 *
	 * @param int $delay maximum lock delay in seconde
	 * @return PhpStorage 
	 */
	public function setMaxLockDelay($delay) {
		$this->maxLockDelay = $delay;
		return $this;
	}

	/**
	 * Control of storage folder
	 */
	protected function checkStorageFolder() {
		$path = rtrim($this->storagePath, DIRECTORY_SEPARATOR);
		if ( !is_dir($path) ) {
			$parentPath = dirname($path);
			if ( !is_writable($parentPath) ) {
				Exception::throwDirectoryNotWritableException($parentPath);
			}
			mkdir($path, 0775, true);
		}
		if ( !is_writable($path) ) {
			Exception::throwDirectoryNotWritableException($path);
		}
	}

	/**
	 * Return the storage path with trailing slash
	 *
	 * @return string
	 */
	public function getStoragePath() {
		$this->checkStorageFolder();
		return $this->storagePath;
	}

	public function loadTableData(Table $table, &$data) {
		$filename = $this->getTableFileName($table);
		$lock = new FileLock($filename);
		if ( $lock->waitUntilLocked($this->maxLockDelay * 1000) ) {
			$this->doLoadTableData($filename, $data);
			if ( !is_array($data) ) {
				Exception::throwInvalidTableDataException($table);
			}
			$lock->unlock();
		}
		else {
			Exception::throwLockedTableException($table);
		}
	}

	public function storeTableData(Table $table, &$data) {
		if ( !is_array($data) ) {
			Exception::throwInvalidTableDataException($table);
		}
		$filename = $this->getTableFileName($table);
		$lock = new FileLock($filename);
		if ( $lock->waitUntilLocked($this->maxLockDelay * 1000) ) {
			$this->doStoreTableData($filename, $data);
			$lock->unlock();
		}
		else {
			Exception::throwLockedTableException($table);
		}
	}

	public function destroyTableData(Table $table) {
		$filename = $this->getTableFileName($table);
		$lock = new FileLock($filename);
		if ( $lock->waitUntilLocked($this->maxLockDelay * 1000) ) {
			$this->doDestroyTableData($filename);
			$lock->unlock();
		}
		else {
			Exception::throwLockedTableException($table);
		}
	}

	public function destroyDatabase(Database $database) {
		$tableNames = $this->getTableList($database);
		foreach ( $tableNames as $tableName ) {
			$this->destroyTableData($database->getTable($tableName));
		}
	}

	abstract protected function getTableFileName(Table $table);

	abstract protected function doDestroyTableData($filename);

	abstract protected function doLoadTableData($filename, &$data);

	abstract protected function doStoreTableData($filename, &$data);
}