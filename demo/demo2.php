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

$table = $database->getTable('vehicle');

print_table('Full Table', $table);

print_table('Update (price > 1500 => price / 10)', function () use ($table) {
    return $table->updateMultiple(
        $table->expr()->gt('price', 1500),
        function ($row) {
            $row['price'] /= 10;
            return $row;
        }
    );
});

print_table('Delete (price < 180 or color = "brown")', function () use ($table) {
    return $table->deleteMultiple(
        $table->expr()->or(
            $table->expr()->lt('price', 180),
            $table->expr()->eq('color', 'brown', false)
        )
    );
});

print_table('Update an existing element', function () use ($table) {
    return $table->update([
        'id'    => 45,
        'price' => '2000',
    ]);
});

print_table('Update the same element but with its key', function () use ($table) {
    return $table->update(['price' => '2100'], 45);
});

print_table('Insert an element', function () use ($table) {
    return $table->insert([
        'mark'  => 'BMW',
        'price' => '3000',
        'color' => 'Green',
    ]);
});

print_table('Upsert an element', function () use ($table) {
    return $table->upsert([
        'mark'  => 'BMW',
        'price' => '3100',
        'color' => 'Red',
    ]);
});

print_table('Upsert the same element', function () use ($table) {
    return $table->upsert(
        ['color' => 'Yellow'],
        $table->getLastInsertedKey()
    );
});

print_table('Delete one element', function () use ($table) {
    return $table->delete(31);
});
