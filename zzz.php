<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\Storage\PhpFileStorage;

include __DIR__ . '/vendor/autoload.php';

$storage = new PhpFileStorage(__DIR__ . '/demo/database');

foreach ($storage->tableNames() as $name) {
    $storage->save($name, array_values($storage->load($name)));
}

echo "\n";
