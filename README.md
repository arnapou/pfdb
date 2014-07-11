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
* NoSQL Database (although Arnapou\PFDB can be used for light key/pair database)

Disclaimer :
__do not use Arnapou\PFDB for huge file, you will naturally use lots of memory and CPU. It is not designed for huge files.__

I have not the time to make documentation, code is enough simple and readable with php docs to be auto-documented.
Examples are the best documentation you will find.

Conditioning
========

    include 'src/autoload.php';
    
    $storage = new \Arnapou\PFDB\Storage\PhpFileStorage($somePath);
    $database = new \Arnapou\PFDB\Database($storage);
    
    $table = $database->getTable('vehicle');
    
    $condition = \Arnapou\PFDB\Condition\ConditionBuilder::createAnd()
        ->greaterThan('price', 10000)
        ->matchRegExp('model', '^C[0-9]+');
        
    $iterator = $table->find($condition)
                      ->sort(array('constructor' => true, 'model' => false))
                      ->limit(0, 50);
                      
    foreach($iterator as $key => $row) {
        // do whatever you want
    }
    
Extending Conditions
=================
Class :

    class IsUppercaseCondition implements \Arnapou\PFDB\Condition\ConditionInterface {

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

    }

Use :

    include 'src/autoload.php';
    
    $storage = new \Arnapou\PFDB\Storage\PhpFileStorage($somePath);
    $database = new \Arnapou\PFDB\Database($storage);
    
    $table = $database->getTable('vehicle');
    
    $condition = \Arnapou\PFDB\Condition\ConditionBuilder::createAnd()
        ->add(new IsUppercaseCondition('model'));
    
    foreach($table->find($condition) as $key => $row) {
        // do whatever you want
    }

Use PFDB Iterator out of storage context
========================================

    include 'src/autoload.php';

    $array = array(
        array('name' => 'John', 'age' => 20),
        array('name' => 'Edith', 'age' => 25),
        array('name' => 'Steve', 'age' => 30),
        array('name' => 'Matthew', 'age' => 22),
    );

    $arrayIterator = new \Arnapou\PFDB\Iterator\ArrayIterator($array);
    $condition = \Arnapou\PFDB\Condition\ConditionBuilder::createAnd()
        ->greaterThan('age', 24);
    $iterator = new \Arnapou\PFDB\Iterator\Iterator($arrayIterator, $condition);

    foreach($iterator as $key => $row) {
        // do whatever you want
    }

Build your own storage
======================
You want to use CSV file instead of php dumped array ?

Easy : extends or implements your own storage and use it to load/store/delete data.

Look at the existing storages and write your own.
