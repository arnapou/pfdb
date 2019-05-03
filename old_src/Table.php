<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB;

use Arnapou\PFDB\Exception\Exception;
use Arnapou\PFDB\Storage\StorageInterface;

class Table implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     *
     * @var string
     */
    protected $name = null;

    /**
     *
     * @var Database
     */
    protected $database = null;

    /**
     *
     * @var StorageInterface
     */
    protected $storage = null;

    /**
     *
     * @var array
     */
    protected $data = [];

    /**
     *
     * @var Iterator\ArrayIterator
     */
    private $dataIterator;

    /**
     *
     * @var bool
     */
    protected $modified = false;

    /**
     * Instanciate a Table object
     *
     * @param Database $database database object
     * @param string   $name     should match ^[a-zA-Z0-9_.-]+$
     */
    public function __construct(Database $database, $name)
    {
        if (!preg_match('!^[a-zA-Z0-9_.-]+$!', $name)) {
            Exception::throwInvalidTableNameException($name);
        }
        $this->database = $database;
        $this->name     = $name;
        $this->storage  = $database->getStorage();
        $this->reload();
    }

    /**
     * Reload table data from database storage object
     *
     * @return Table
     */
    public function reload()
    {
        $this->modified = false;
        $this->storage->loadTableData($this, $this->data);
        $this->dataIterator = new Iterator\ArrayIterator($this->data);
        return $this;
    }

    /**
     * Set a value for a specified key
     *
     * If key is NULL, then it auto-increments the key like php array
     *
     * @param mixed $key   integer, string or null
     * @param mixed $value array or simple value
     * @return mixed
     */
    public function set($key, $value)
    {
        if ($key === null) {
            if (empty($this->data)) {
                $this->data[1] = $value;
            } else {
                $this->data[] = $value;
            }
            end($this->data);
            $key = key($this->data);
        } else {
            $this->data[$key] = $value;
        }
        $this->modified = true;
        return $key;
    }

    /**
     * Get the value for a specified key
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * Delete rows which match the condition
     *
     * The condition can be either :
     * - ConditionInterface object
     * - ConditionBuilder object
     * - single key
     *
     * @param mixed $condition
     * @return Table
     */
    public function delete($condition)
    {
        $iterator       = $this->getIterator()->find($condition);
        $offsetToDelete = [];
        foreach ($iterator as $key => $row) {
            $offsetToDelete[] = $key;
        }
        foreach ($offsetToDelete as $offset) {
            $this->offsetUnset($offset);
        }
        return $this;
    }

    /**
     * Update rows which match the condition with a valid php callable.
     *
     * The condition can be either :
     * - ConditionInterface object
     * - ConditionBuilder object
     * - single key
     *
     * @param mixed    $condition
     * @param callable $callable   Receive one parameter which is the current row.
     *                             The callable should return the updated row.
     * @return Table
     */
    public function update($condition, $callable)
    {
        if (!\is_callable($callable)) {
            Exception::throwBadArgumentTypeException('callable');
        }
        $iterator = $this->getIterator()->find($condition);
        foreach ($iterator as $key => $row) {
            $this->offsetSet($key, \call_user_func($callable, $row));
        }
        return $this;
    }

    /**
     * Find rows which match the condition.
     *
     * The condition can be either :
     * - ConditionInterface object
     * - ConditionBuilder object
     * - single key
     *
     * @param mixed $condition
     */
    public function find($condition)
    {
        return $this->getIterator()->find($condition);
    }

    /**
     * Find all rows
     *
     */
    public function findAll()
    {
        return $this->getIterator();
    }

    /**
     * Find rows which match the condition and return the first row if it exists.
     *
     * It returns NULL if there is no rows to return.
     *
     * The condition can be either :
     * - ConditionInterface object
     * - ConditionBuilder object
     * - single key
     *
     * @param mixed|array $condition
     * @return mixed
     */
    public function findOne($condition)
    {
        $results = $this->find($condition);
        if (null !== $results) {
            foreach ($results as $result) {
                return $result;
            }
        }
        return null;
    }

    /**
     * Return the database of this table
     *
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Return the name of this table
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Flush modifications via database storage object
     *
     * @return Table
     */
    public function flush()
    {
        if ($this->isModified()) {
            $this->storage->storeTableData($this, $this->data);
        }
        return $this;
    }

    /**
     * Empty the table
     *
     * @return Table
     */
    public function clear()
    {
        $this->data = [];
        return $this;
    }

    /**
     * Drop the table via database storage object
     *
     * @return Table
     */
    public function drop()
    {
        $this->clear();
        $this->modified = false;
        $this->storage->destroyTableData($this);
        return $this;
    }

    /**
     * Tells whether the table is modified or not
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->modified;
    }

    /**
     * return the raw data array of this table
     *
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetUnset($offset)
    {
        $this->modified = true;
        unset($this->data[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function count()
    {
        return \count($this->data);
    }

    /**
     *
     * @return Iterator\ArrayIterator
     */
    public function getIterator()
    {
        return $this->dataIterator;
    }
}
