<?php

namespace PFDB\Storage;

use PFDB\Database;
use PFDB\Table;

interface StorageInterface {

	/**
	 *
	 * @param Table $table
	 * @param array $data
	 */
	public function loadTableData(Table $table, &$data);

	/**
	 *
	 * @param Table $table
	 * @param array $data
	 */
	public function storeTableData(Table $table, &$data);

	/**
	 *
	 * @param Table $table
	 */
	public function destroyTableData(Table $table);

	/**
	 * 
	 * @param Database $database
	 */
	public function destroyDatabase(Database $database);
}