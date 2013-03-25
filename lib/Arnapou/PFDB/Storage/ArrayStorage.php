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
use Arnapou\PFDB\Table;

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