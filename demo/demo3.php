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

/**
 * Entity Mark
 */
class Mark extends Arnapou\PFDB\ORM\BaseEntity {

    protected $name;

    public function __toString() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}

/**
 * Entity Color
 */
class Color extends Arnapou\PFDB\ORM\BaseEntity {

    protected $name;

    public function __toString() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}

/**
 * Entity Vehicle
 */
class Vehicle extends Arnapou\PFDB\ORM\BaseEntity {

    protected $mark;
    protected $color;
    protected $price;

    public function getMark() {
        $this->__load('mark');
        return $this->mark;
    }

    public function setMark($mark) {
        $this->mark = $mark;
    }

    public function getColor() {
        $this->__load('color');
        return $this->color;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

}

/**
 * SCHEMA
 */
$schema = Arnapou\PFDB\ORM\Schema\Schema::fromArray(array(
        'entities' => array(
            array(
                'name'       => 'vehicle_orm',
                'class'      => 'Vehicle',
                'attributes' => array(
                    'price' => array('type' => 'string'),
                ),
                'links'      => array(
                    'mark'  => array(
                        'type'   => 'many_to_one',
                        'entity' => 'mark',
                        'field'  => 'mark_id',
                    ),
                    'color' => array(
                        'type'   => 'many_to_one',
                        'entity' => 'color',
                        'field'  => 'color_id',
                    ),
                ),
            ),
            array(
                'name'       => 'color',
                'class'      => 'Color',
                'attributes' => array(
                    'name' => array('type' => 'string'),
                ),
            ),
            array(
                'name'       => 'mark',
                'class'      => 'Mark',
                'attributes' => array(
                    'name' => array('type' => 'string'),
                ),
            ),
        ),
    ));

/**
 * EXAMPLE
 */
$storage  = new Arnapou\PFDB\Storage\PhpFileStorage(__DIR__ . '/database');
$database = new Arnapou\PFDB\ORM\Database($storage, $schema);
$database->setAutoFlush(false); // avoid automatic save at end of script

$table = $database->getTable('vehicle_orm');

print_title('Entities');

print_table('Full Table', $table);

print_table('Find (price > 1500 and color = "Red")', function() use ($table, $database) {
    return $table->find(
        ConditionBuilder::createAnd()
            ->greaterThan('price', 1500)
            ->equalTo('color', 
                $database->getTable('color')
                    ->findOne(array('name' => 'Red'))
            )
    );
});

print_table('Find (mark = "Citroen" and color = "Brown")', function() use ($table) {
    return $table->find(array(
        'color.name' => 'Brown',
        'mark.name'  => 'Citroen'
    ));
});

print_table('Find (price > 1500 or color = "Red")', function() use ($table) {
    return $table->find(
        ConditionBuilder::createOr()
            ->greaterThan('price', 1500)
            ->equalTo('color.name', 'Red')
    );
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC)', function() use ($table) {
    return $table->find(
            ConditionBuilder::createOr()
            ->greaterThan('price', 1500)
            ->equalTo('color.name', 'Red')
        )
        ->sort(array(
            'mark.name' => true,
            'price'     => false,
    ));
});

print_table('Find (price > 1500 or color = "Red") sorted (mark ASC then price DESC) limit (1, 3)', function() use ($table) {
    return $table->find(
            ConditionBuilder::createOr()
            ->greaterThan('price', 1500)
            ->equalTo('color.name', 'Red')
        )
        ->sort(array(
            'mark.name' => true,
            'price'     => false,
        ))
        ->limit(1, 3);
});
