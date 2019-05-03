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
use Symfony\Component\Yaml\Yaml;

class YamlFileStorage extends AbstractFileStorage
{
    public function __construct($storagePath, $idField = 'id')
    {
        parent::__construct($storagePath);
    }

    protected function getTableFileName(Table $table)
    {
        return $this->getStoragePath() . 'table.' . $table->getName() . '.yaml';
    }

    protected function doLoadTableData($filename, &$data)
    {
        if (!is_file($filename)) {
            file_put_contents($filename, Yaml::dump());
        }
        $data = include($filename);
    }

    protected function doStoreTableData($filename, &$data)
    {
        file_put_contents($filename, Yaml::dump($data));
    }

    protected function doDestroyTableData($filename)
    {
        unlink($filename);
    }

    public function getTableList(Database $database)
    {
        $files = glob($this->getStoragePath() . 'table.*.yaml', GLOB_NOSORT) ?: [];
        $tableNames = [];
        foreach ($files as $file) {
            $tableName    = basename($file, '.yaml');
            $tableName    = str_replace('table.', '', $tableName);
            $tableNames[] = $tableName;
        }
        return $tableNames;
    }

//    private function
}
