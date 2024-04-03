<?php

declare(strict_types=1);

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
use Arnapou\PFDB\Table;

require __DIR__ . '/src/bootstrap.php';

(new Page(__FILE__))(
    static function () {
        $storage = new PhpFileStorage(__DIR__ . '/database');
        $database = new DatabaseReadonly($storage);

        /** @var Table $colors */
        $colors = $database->getTable('color');
        /** @var Table $marks */
        $marks = $database->getTable('mark');
        /** @var Table $vehicles */
        $vehicles = $database->getTable('vehicle_linked');

        showTable('Colors', $colors);
        showTable('Marks', $marks);
        showTable('Vehicles', $vehicles);

        showTable(
            'All vehicles with corresponding names',
            static fn () => $vehicles->find()
                ->select(
                    'id',
                    $vehicles->fields()->parent(
                        'color_id',  // field name
                        $colors,     // foreign table
                        'name'       // foreign name
                    ),
                    $vehicles->fields()->parent(
                        'mark_id',
                        $marks
                    ),
                    'price'
                )
        );

        showTable(
            'Filter on color name without displaying it',
            static fn () => $vehicles->find(
                $vehicles->expr()->eq(
                    $vehicles->fields()->parent(
                        'color_id',  // field name
                        $colors,     // foreign table
                        'name'       // foreign name
                    ),
                    'Red'
                )
            )
        );

        showTable(
            'Multiple filters (color + mark)',
            static fn () => $vehicles->find(
                $vehicles->expr()->eq(
                    $vehicles->fields()->parent(
                        'color_id',  // field name
                        $colors,     // foreign table
                        'name'       // foreign name
                    ),
                    'Red'
                ),
                $vehicles->expr()->contains(
                    $vehicles->fields()->parent(
                        'mark_id',   // field name
                        $marks,      // foreign table
                        'name'       // foreign name
                    ),
                    'o'
                )
            )
        );
    }
);
