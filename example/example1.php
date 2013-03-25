<?php

use Arnapou\PFDB\Query\QueryBuilder;

include __DIR__ . '/functions.php';
include __DIR__ . '/../lib/autoload.php';

$storage = new Arnapou\PFDB\Storage\PhpStorage(__DIR__ . '/../database');
$database = new Arnapou\PFDB\Database($storage);

$table = $database->getTable('vehicle');

print_title('Querying');

print_table('Full Table', $table);

print_table('Find (price > 1500)', $table->find(
		QueryBuilder::createAnd()
			->greaterThan('price', 1500)
	)
);

print_table('Find (price > 1500 and color = "Red")',
	$table->find(
		QueryBuilder::createAnd()
			->greaterThan('price', 1500)
			->equalTo('color', 'Red')
	)
);

print_table('Find (price > 1500 or color = "Red")',
	$table->find(
		QueryBuilder::createOr()
			->greaterThan('price', 1500)
			->equalTo('color', 'Red')
	)
);

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC)',
	$table->find(
			QueryBuilder::createOr()
			->greaterThan('price', 1500)
			->equalTo('color', 'Red')
		)
		->sort(array(
			'mark' => true,
			'price' => false,
		))
);

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3)',
	$table->find(
			QueryBuilder::createOr()
			->greaterThan('price', 1500)
			->equalTo('color', 'Red')
		)
		->sort(array(
			'mark' => true,
			'price' => false,
		))
		->limit(1, 3)
);

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3) find (color match regexp /w/) sorted (price ASC)',
	$table->find(
			QueryBuilder::createOr()
			->greaterThan('price', 1500)
			->equalTo('color', 'Red')
		)
		->sort(array(
			'mark' => true,
			'price' => false,
		))
		->limit(1, 3)
		->find(QueryBuilder::createAnd()
			->matchRegExp('color', 'w')
		)
		->sort(array('price' => true))
);

print_table('Find ((price > 1600 and color = "Red") or (price < 1600 and color = "Green")) ',
	$table->find(
		QueryBuilder::createOr()
			->add(QueryBuilder::createAnd()
				->greaterThan('price', 1600)
				->equalTo('color', 'Red')
			)
			->add(QueryBuilder::createAnd()
				->lowerThan('price', 1600)
				->equalTo('color', 'Green')
			)
	)
);

print_table('Find (-key- IN 52,31,89) sorted (price ASC)',
	$table->find(
			QueryBuilder::createAnd()
			->in(null, array(52, 31, 89))
		)
		->sort(array(
			'price' => true
		))
);