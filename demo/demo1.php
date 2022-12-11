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
use Arnapou\PFDB\Query\Field\KeyField;
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
            'Find (price > 1500)',
            static fn () => $table->find(
                $table->expr()->gt('price', 1500)
            )
        );

        showTable(
            'Find (price > 1500 and color = "Red")',
            static fn () => $table->find(
                $table->expr()->gt('price', 1500),
                $table->expr()->eq('color', 'Red')
            )
        );

        showTable(
            'Find (price > 1500 or color = "Red")',
            static fn () => $table->find(
                $table->expr()->or(
                    $table->expr()->gt('price', 1500),
                    $table->expr()->eq('color', 'Red')
                )
            )
        );

        showTable(
            'Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC)',
            static fn () => $table->find(
                $table->expr()->or(
                    $table->expr()->gt('price', 1500),
                    $table->expr()->eq('color', 'Red')
                )
            )
                ->sort('mark', ['price', 'DESC'])
        );

        showTable(
            'Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3)',
            static fn () => $table->find(
                $table->expr()->or(
                    $table->expr()->gt('price', 1500),
                    $table->expr()->eq('color', 'Red')
                )
            )
                ->sort('mark', ['price', 'DESC'])
                ->limit(1, 3)
        );

        showTable(
            'Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3)'
            . ' find (color match regexp /w/) sorted (price ASC)',
            static fn () => $table->find(
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
                ->sort('price')
        );

        showTable(
            'Find ((price > 1600 and color = "Red") or (price < 1600 and color = "Green")) ',
            static fn () => $table->find(
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
            )
        );

        showTable(
            'Find (:key IN 52,31,89) sorted (price ASC)',
            static fn () => $table->find(
                $table->expr()->in('id', [52, 31, 89])
            )
                ->sort('price')
        );

        showTable(
            'Find (:key IN 52,31,89) sorted (price ASC) but filtered on key',
            static fn () => $table->find(
                $table->expr()->in(new KeyField(), [52, 31, 89])
            )
                ->sort('price')
        );
    }
);
