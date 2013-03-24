<?php

use PFDB\Query\QueryBuilder;

include __DIR__ . '/functions.php';
include __DIR__ . '/../lib/autoload.php';

$storage = new PFDB\Storage\PhpStorage(__DIR__ . '/../database');
$database = new PFDB\Database($storage);
$database->setAutoFlush(false); // avoid automatic save at end of script

$table = $database->getTable('vehicle');

print_title('Updating / Deleting');

print_table('Full Table', $table);

print_table('Update (price > 1500 => price / 10)',
	$table->update(
		QueryBuilder::createAnd()
			->greaterThan('price', 1500)
		, function($row) {
			$row['price'] /= 10;
			return $row;
		}
	)
);

print_table('Delete (price < 180 or color = "brown")',
	$table->delete(
		QueryBuilder::createOr()
			->lowerThan('price', 180)
			->equalTo('color', 'brown', false)
	)
);
