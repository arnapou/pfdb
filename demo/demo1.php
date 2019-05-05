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

print_table('Find (price > 1500)', function () use ($table) {
    return $table->find(
        $table->expr()->gt('price', 1500)
    );
});

print_table('Find (price > 1500 and color = "Red")', function () use ($table) {
    return $table->find(
        $table->expr()->gt('price', 1500),
        $table->expr()->eq('color', 'Red')
    );
});

print_table('Find (price > 1500 or color = "Red")', function () use ($table) {
    return $table->find(
        $table->expr()->or(
            $table->expr()->gt('price', 1500),
            $table->expr()->eq('color', 'Red')
        )
    );
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC)', function () use ($table) {
    return $table->find(
        $table->expr()->or(
            $table->expr()->gt('price', 1500),
            $table->expr()->eq('color', 'Red')
        )
    )
        ->sort('mark', ['price', 'DESC']);
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3)', function () use ($table) {
    return $table->find(
        $table->expr()->or(
            $table->expr()->gt('price', 1500),
            $table->expr()->eq('color', 'Red')
        )
    )
        ->sort('mark', ['price', 'DESC'])
        ->limit(1, 3);
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3) find (color match regexp /w/) sorted (price ASC)', function () use ($table) {
    return $table->find(
        $table->expr()->or(
            $table->expr()->gt('price', 1500),
            $table->expr()->eq('color', 'Red')
        )
    )
        ->sort('mark', ['price', 'DESC'])
        ->limit(1, 3)
        ->chain()
        ->where(
            $table->expr()->match('color', 'w')
        )
        ->sort('price');
});

print_table('Find ((price > 1600 and color = "Red") or (price < 1600 and color = "Green")) ', function () use ($table) {
    return $table->find(
        $table->expr()->or(
            $table->expr()->and(
                $table->expr()->gt('price', 1600),
                $table->expr()->eq('color', 'Red')
            ),
            $table->expr()->and(
                $table->expr()->lt('price', 1600),
                $table->expr()->eq('color', 'Green')
            )
        )
    );
});

print_table('Find (:key IN 52,31,89) sorted (price ASC)', function () use ($table) {
    return $table->find(
        $table->expr()->in('id', [52, 31, 89])
    )
        ->sort('price');
});

print_table('Find (:key IN 52,31,89) sorted (price ASC) but filtered on key', function () use ($table) {
    return $table->find(
        $table->expr()->in($table->expr()->keyField(), [52, 31, 89])
    )
        ->sort('price');
});
