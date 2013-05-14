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

class PhpFileStorage extends AbstractFileStorage {

	protected function getTableFileName(Table $table) {
		return $this->getStoragePath() . 'table.' . $table->getName() . '.php';
	}

	protected function doLoadTableData($filename, &$data) {
		if ( !is_file($filename) ) {
			file_put_contents($filename, "<?php return array();\n");
		}
sleep(3);
		$data = include($filename);
	}

	protected function doStoreTableData($filename, &$data) {
		file_put_contents($filename, "<?php return " . var_export($data, true) . ";\n");
	}

	protected function doDestroyTableData($filename) {
		unlink($filename);
	}

	public function getTableList(Database $database) {
		$files = glob($this->getStoragePath() . 'table.*.php', GLOB_NOSORT);
		if ( is_array($files) ) {
			$tableNames = array();
			foreach ( $files as $file ) {
				$tableName = basename($file, '.php');
				$tableName = str_replace('table.', '', $tableName);
				$tableNames[] = $tableName;
			}
			return $tableNames;
		}
		else {
			return array();
		}
	}

}