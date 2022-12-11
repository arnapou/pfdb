<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou Weather package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\DatabaseReadonly;
use Arnapou\PFDB\Storage\PhpFileStorage;
use Arnapou\PFDB\Table;

require __DIR__ . '/src/bootstrap.php';

(new Page(__FILE__))(
    static function () {
        $storage = new PhpFileStorage(__DIR__ . '/database');
        $database = new DatabaseReadonly($storage);

        /** @var Table $table */
        $table = $database->getTable('vehicle');

        showTable('Full Table', $table);

        showTable(
            'Update (price > 1500 => price / 10)',
            static fn () => $table->updateMultiple(
                expr()->gt('price', 1500),
                function ($row) {
                    $row['price'] /= 10;

                    return $row;
                }
            )
        );

        showTable(
            'Delete (price < 180 or color = "brown")',
            static fn () => $table->deleteMultiple(
                expr()->or(
                    expr()->lt('price', 180),
                    expr()->eq('color', 'brown', false)
                )
            )
        );

        showTable(
            'Update an existing element',
            static fn () => $table->update(
                [
                    'id' => 45,
                    'price' => '2000',
                ]
            )
        );

        showTable(
            'Update the same element but with its key',
            static fn () => $table->update(['price' => '2100'], 45)
        );

        showTable(
            'Insert an element',
            static fn () => $table->insert(
                [
                    'mark' => 'BMW',
                    'price' => '3000',
                    'color' => 'Green',
                ]
            )
        );

        showTable(
            'Upsert an element',
            static fn () => $table->upsert(
                [
                    'mark' => 'BMW',
                    'price' => '3100',
                    'color' => 'Red',
                ]
            )
        );

        showTable(
            'Upsert the same element',
            static fn () => $table->upsert(
                ['color' => 'Yellow'],
                $table->getLastInsertedKey()
            )
        );

        showTable(
            'Delete one element',
            static fn () => $table->delete(31)
        );
    }
);
