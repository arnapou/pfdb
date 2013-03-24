<?php

namespace PFDB\Storage;

use PFDB\Exception\Exception;
use PFDB\Database;
use PFDB\Table;

abstract class AbstractFileStorage implements StorageInterface {

	/**
	 * 
	 * @var string
	 */
	private $storagePath;

	/**
	 * 
	 * @param string $storagePath path where the tables are stored
	 * @param bool $autoCreate auto-create folder
	 */
	public function __construct($storagePath) {
		$this->storagePath = rtrim(rtrim($storagePath, '\\'), '/') . DIRECTORY_SEPARATOR;
		$this->checkStorageFolder();
	}

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

}