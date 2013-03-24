Introduction
============

What it is :
* Pure POO (php 5.3 required)
* Lightweight
* As fast as it can
* Extendable (interfaces, ...)

What it is _not_ :
* SQL database
* Relational database
* ORM
* DBDAL
* NoSQL Database (although PFDB can be used for light key/pair database)

Disclaimer :
__do not use PFDB for huge file, you will naturally use lots of memory and CPU. It is not designed for huge files.__

I have not the time to make documentation, code is enough simple and readable with php docs to be auto-documented.
Examples are the best documentation you will find.

Querying
========

    include 'lib/autoload.php';
    
    $storage = new \PFDB\Storage\PhpStorage($somePath);
    $database = new \PFDB\Database($storage);
    
    $table = $database->getTable('vehicle');
    
    $query = \PFDB\Query\QueryBuilder::createAnd()
        ->greaterThan('price', 10000)
        ->matchRegExp('model', '^C[0-9]+');
        
    $iterator = $table->find($query)
                      ->sort(array('constructor' => true, 'model' => false))
                      ->limit(0, 50);
                      
    foreach($iterator as $key => $row) {
        // do whatever you want
    }
    
Extending Queries
=================
Class :

    class IsUppercaseQuery implements \PFDB\Query\QueryInterface {

        protected $field;

        public function __construct($field) {
            $this->field = $field;
        }

        public function match($key, $value) {
            if(!isset($value[$this->field]) {
                return false;
            }
            $testedValue = (string)$value[$this->field];
            $isUppercase = ($testedValue === strtoupper($testedValue));
            return $isUppercase;
        }

        public function toArray() {
            // only used in some cases I don't want to explain here
            return array();
        }
    }

Use :

    include 'lib/autoload.php';
    
    $storage = new \PFDB\Storage\PhpStorage($somePath);
    $database = new \PFDB\Database($storage);
    
    $table = $database->getTable('vehicle');
    
    $query = \PFDB\Query\QueryBuilder::createAnd()
        ->add(new IsUppercaseQuery('model'));
    
    foreach($table->find($query) as $key => $row) {
        // do whatever you want
    }

Use PFDB Iterator out of storage concept
========================================

    include 'lib/autoload.php';

    $array = array(
        array('name' => 'John', 'age' => 20),
        array('name' => 'Edith', 'age' => 25),
        array('name' => 'Steve', 'age' => 30),
        array('name' => 'Matthew', 'age' => 22),
    );

    $arrayIterator = new \PFDB\Iterator\ArrayIterator($array);
    $query = \PFDB\Query\QueryBuilder::createAnd()
        ->greaterThan('age', 24);
    $iterator = new \PFDB\Iterator\Iterator($arrayIterator, $query);

    foreach($iterator as $key => $row) {
        // do whatever you want
    }


Build your own storage
======================
You want to use CSV file instead of php dumped array ?

Easy : extends or implements your own storage and use it to load/store/delete data.

Look at the existing storages and write your own.
