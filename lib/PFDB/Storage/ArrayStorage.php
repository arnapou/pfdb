<?php

namespace PFDB\Storage;

use PFDB\Exception\Exception;
use PFDB\Table;

class ArrayStorage implements StorageInterface {

	protected $data;

	public function __construct(&$data) {
		$this->data = $data;
	}

	public function loadTableData(Table $table, &$data) {
		$data = $this->data;
	}

	public function storeTableData(Table $table, &$data) {
		// nothing to do
	}

	public function destroyTableData(Table $table) {
		// nothing to do
	}

	public function destroyDatabase(Database $database) {
		// nothing to do
	}

}