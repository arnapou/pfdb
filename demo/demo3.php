<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\DatabaseReadonly;
use Arnapou\PFDB\Storage\PhpFileStorage;

include __DIR__ . '/functions.php';
include __DIR__ . '/../vendor/autoload.php';

$storage  = new PhpFileStorage(__DIR__ . '/database');
$database = new DatabaseReadonly($storage);

$colors   = $database->getTable('color');
$marks    = $database->getTable('mark');
$vehicles = $database->getTable('vehicle_linked');

print_table('Colors', $colors);
print_table('Marks', $marks);
print_table('Vehicles', $vehicles);

print_table('All vehicles with corresponding names', function () use ($colors, $marks, $vehicles) {
    return $vehicles->find()
        ->select(
            'id',
            $vehicles->expr()->foreignField(
                'color_id',  // field name
                $colors,     // foreign table
                'name'       // foreign name
            ),
            $vehicles->expr()->foreignField(
                'mark_id',
                $marks
            ),
            'price'
        );
});

print_table('Filter on color name without displaying it', function () use ($colors, $marks, $vehicles) {
    return $vehicles->find(
        $vehicles->expr()->eq(
            $vehicles->expr()->foreignField(
                'color_id',  // field name
                $colors,     // foreign table
                'name'       // foreign name
            ),
            'Red'
        )
    );
});

print_table('Multiple filters (color + mark)', function () use ($colors, $marks, $vehicles) {
    return $vehicles->find(
        $vehicles->expr()->eq(
            $vehicles->expr()->foreignField(
                'color_id',  // field name
                $colors,     // foreign table
                'name'       // foreign name
            ),
            'Red'
        ),
        $vehicles->expr()->contains(
            $vehicles->expr()->foreignField(
                'mark_id',  // field name
                $marks,     // foreign table
                'name'       // foreign name
            ),
            'o'
        )
    );
});
