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

class PhpStorage extends AbstractFileStorage {

	protected function getTableFileName(Table $table) {
		return $this->getStoragePath() . 'table.' . $table->getName() . '.php';
	}

	public function loadTableData(Table $table, &$data) {
		$filename = $this->getTableFileName($table);
		if ( !is_file($filename) ) {
			file_put_contents($filename, "<?php return array();\n");
		}
		$data = include($filename);
		if ( !is_array($data) ) {
			Exception::throwInvalidTableDataException($table);
		}
	}

	public function storeTableData(Table $table, &$data) {
		if ( !is_array($data) ) {
			Exception::throwInvalidTableDataException($table);
		}
		$filename = $this->getTableFileName($table);
		file_put_contents($filename, "<?php return " . var_export($data, true) . ";\n");
	}

	public function destroyTableData(Table $table) {
		$filename = $this->getTableFileName($table);
		unlink($filename);
	}

	public function destroyDatabase(Database $database) {
		$files = glob($this->getStoragePath() . 'table.*.php', GLOB_NOSORT);
		if ( is_array($tables) ) {
			foreach ( $files as $file ) {
				unlink($file);
			}
		}
	}

}