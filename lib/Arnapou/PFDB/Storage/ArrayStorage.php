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
use Arnapou\PFDB\Database;

class ArrayStorage implements StorageInterface {

	protected $data = array();

	public function __construct(&$data = null) {
		if ( is_array($data) ) {
			$this->data = $data;
		}
	}

	public function loadTableData(Table $table, &$data) {
		if ( !isset($this->data[$table->getName()]) ) {
			$this->data[$table->getName()] = array();
		}
		$data = $this->data[$table->getName()];
	}

	public function storeTableData(Table $table, &$data) {
		$this->data[$table->getName()] = $data;
	}

	public function destroyTableData(Table $table) {
		unset($this->data[$table->getName()]);
	}

	public function destroyDatabase(Database $database) {
		$this->data = array();
	}

	public function getTableList(Database $database) {
		$list = array_keys($this->data);
		sort($list);
		return $list;
	}

}