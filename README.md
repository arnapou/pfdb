Introduction
============

![pipeline](https://gitlab.com/arnapou/pfdb/badges/master/pipeline.svg)
![coverage](https://gitlab.com/arnapou/pfdb/badges/master/coverage.svg?job=coverage)

What it is :

* Pure POO
* Lightweight
* Extendable (interfaces, ...)

What it is _not_ :

* SQL database
* Relational database
* ORM
* DBDAL

When to use : 

* You absolutely want flat files
* More read than write (even if you can use lock strategy thanks to `symfony/lock`)
* Lightweight data
* Simple files like configs or small data (less than a few thousands items)

Implemented files formats :

* YAML
* PHP

_Note that is is really easy to make your own implementation_


There is not a lot of documentation because I did this project for me and I guess a few examples and reading the code should be enough for developers.
Examples are the best documentation you will find.

A few examples : https://pfdb.arnapou.net/

Conditioning
========
```php
$storage = new \Arnapou\PFDB\Storage\PhpFileStorage($somePath);
$database = new \Arnapou\PFDB\Database($storage);

$table = $database->getTable('vehicle');

$expr = $table->expr()->and(
     $table->expr()->gt('price', 10000),
     $table->expr()->match('model', '^C[0-9]+')
);
    
$iterator = $table->find($expr)
                  ->sort('constructor' , ['model' , 'DESC'])
                  ->limit(0, 50);
                  
foreach($iterator as $key => $row) {
    // do whatever you want
}
```
    
Extending Expressions
=================
Class :

```php
class IsUppercaseExpr implements \Arnapou\PFDB\Query\Helper\Expr\ExprInterface {

    private $field;
    
    public function __construct(string $field) 
    {
        $this->field = $field;
    }

    public function __invoke(array $row, $key = null): bool
    {
        if(!isset($row[$this->field]) {
            return false;
        }
        $testedValue = (string)$row[$this->field];
        return $testedValue === strtoupper($testedValue);
    }

}
```

Use :

```php
$storage = new \Arnapou\PFDB\Storage\PhpFileStorage($somePath);
$database = new \Arnapou\PFDB\Database($storage);

$table = $database->getTable('vehicle');

$expr = new IsUppercaseExpr('model');

foreach($table->find($expr) as $key => $row) {
    // do whatever you want
}
```

Use PFDB Iterator out of storage context
========================================

if you just want to select, filter, sort, limit, group, order any iterator 

```php
$data = [
    ['name' => 'John', 'age' => 20],
    ['name' => 'Edith', 'age' => 25],
    ['name' => 'Steve', 'age' => 30],
    ['name' => 'Matthew', 'age' => 22],
);

$query = (new \Arnapou\PFDB\Query\Query())
    ->from(new \ArrayIterator($data))
    ->where($query->expr()->gt('age', 24));

foreach($query as $key => $row) {
    // do whatever you want
}
```

Build your own storage
======================
You want to use CSV file instead of php dumped array ?

Easy : extends or implements your own storage and use it to load/store/delete data.

Look at the existing storages and write your own.

### Changelog versions techniques

| Date       | Tag, Branche | Php | 
|------------|--------------|-----|
| 11/12/2022 | 5.x, master  | 8.2 |
| 30/01/2022 | 4.x          | 8.1 |
| 15/05/2021 | 3.x          | 8.0 |
| 27/02/2019 | 2.x          | 7.2 |
| 07/11/2013 | 1.x          | 5.4 |
