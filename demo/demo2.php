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
include __DIR__ . '/../src/autoload.php';

$storage = new Arnapou\PFDB\Storage\PhpFileStorage(__DIR__ . '/database');
$database = new Arnapou\PFDB\Database($storage);
$database->setAutoFlush(false); // avoid automatic save at end of script

$table = $database->getTable('vehicle');

print_title('Updating / Deleting');

print_table('Full Table', $table);

print_table('Update (price > 1500 => price / 10)', function () use ($table) {
    return $table->update(
        ConditionBuilder::createAnd()
            ->greaterThan('price', 1500)
        , function ($row) {
        $row['price'] /= 10;
        return $row;
    }
    );
});

print_table('Delete (price < 180 or color = "brown")', function () use ($table) {
    return $table->delete(
        ConditionBuilder::createOr()
            ->lowerThan('price', 180)
            ->equalTo('color', 'brown', false)
    );
});
