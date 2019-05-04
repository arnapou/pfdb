<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include __DIR__ . '/vendor/autoload.php';


$storage = new \Arnapou\PFDB\Storage\PhpFileStorage(__DIR__ . '/demo/database');

$table = new \Arnapou\PFDB\Table('vehicle', $storage);

var_dump($table->count());


$query = new \Arnapou\PFDB\Query\Query($table);
//$query->select(['id']);

$query->group(
    ['color'],
    ['marks'=>[]],
    function ($group, $row) {
        $group['marks'][] = $row['mark'];
        return $group;
    },
    function ($group) {
        return $group + ['count'=>\count($group['marks'])];
    }
);
//$query->limit(1, 3);

//$query->addOrderBy('price', 'DESC')->addOrderBy('mark');

foreach ($query as $row) {
    echo json_encode($row) . "\n";
}

echo "\n";
