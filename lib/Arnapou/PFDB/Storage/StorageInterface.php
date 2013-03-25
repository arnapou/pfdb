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

use Arnapou\PFDB\Database;
use Arnapou\PFDB\Table;

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