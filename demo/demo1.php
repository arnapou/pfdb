<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\Condition\ConditionBuilder;

include __DIR__ . '/functions.php';
include __DIR__ . '/../vendor/autoload.php';

$storage = new Arnapou\PFDB\Storage\PhpFileStorage(__DIR__ . '/database');
$database = new Arnapou\PFDB\Database($storage);
$database->setAutoFlush(false); // avoid automatic save at end of script

$table = $database->getTable('vehicle');

print_title('Conditions');

print_table('Full Table', $table);

print_table('Find (price > 1500)', function () use ($table) {
    return $table->find(
        ConditionBuilder::AND()
            ->greaterThan('price', 1500)
    );
});

print_table('Find (price > 1500 and color = "Red")', function () use ($table) {
    return $table->find(
        ConditionBuilder::AND()
            ->greaterThan('price', 1500)
            ->equalTo('color', 'Red')
    );
});

print_table('Find (price > 1500 or color = "Red")', function () use ($table) {
    return $table->find(
        ConditionBuilder::OR()
            ->greaterThan('price', 1500)
            ->equalTo('color', 'Red')
    );
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC)', function () use ($table) {
    return $table->find(
        ConditionBuilder::OR()
            ->greaterThan('price', 1500)
            ->equalTo('color', 'Red')
    )
        ->sort([
            'mark' => true,
            'price' => false,
        ]);
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3)', function () use ($table) {
    return $table->find(
        ConditionBuilder::OR()
            ->greaterThan('price', 1500)
            ->equalTo('color', 'Red')
    )
        ->sort([
            'mark' => true,
            'price' => false,
        ])
        ->limit(1, 3);
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3) find (color match regexp /w/) sorted (price ASC)', function () use ($table) {
    return $table->find(
        ConditionBuilder::OR()
            ->greaterThan('price', 1500)
            ->equalTo('color', 'Red')
    )
        ->sort([
            'mark' => true,
            'price' => false,
        ])
        ->limit(1, 3)
        ->find(
            ConditionBuilder::AND()
                ->matchRegExp('color', 'w')
        )
        ->sort(['price' => true]);
});

print_table('Find ((price > 1600 and color = "Red") or (price < 1600 and color = "Green")) ', function () use ($table) {
    return $table->find(
        ConditionBuilder::OR()
            ->add(
                ConditionBuilder::AND()
                ->greaterThan('price', 1600)
                ->equalTo('color', 'Red')
            )
            ->add(
                ConditionBuilder::AND()
                ->lowerThan('price', 1600)
                ->equalTo('color', 'Green')
            )
    );
});

print_table('Find (-key- IN 52,31,89) sorted (price ASC)', function () use ($table) {
    return $table->find(
        ConditionBuilder::AND()
            ->in(null, [52, 31, 89])
    )
        ->sort([
            'price' => true,
        ]);
});
