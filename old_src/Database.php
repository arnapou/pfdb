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

class Database
{
    /**
     *
     * @var array
     */
    protected $tables = [];

    /**
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     *
     * @var bool
     */
    protected $autoFlush;

    /**
     *
     * @var string
     */
    protected $tableClass;

    /**
     *
     * @var array
     */
    protected $defaultConfig = [
        'autoflush'  => true,
        'tableclass' => 'Arnapou\PFDB\Table',
    ];

    /**
     * Instanciate the database object.
     *
     * It needs a storage object to load/store/drop table
     *
     * @param StorageInterface $storage
     * @param array            $config options :
     *                                 - autoflush : boolean (default = true)
     *                                 - tableclass : string (default = 'Arnapou\PFDB\Table')
     */
    public function __construct(StorageInterface $storage, $config = [])
    {
        $this->storage = $storage;

        $config = array_merge($this->defaultConfig, $config);

        $this->setAutoFlush($config['autoflush']);
        $this->setTableClass($config['tableclass']);
    }

    /**
     * Called when script exits.
     *
     * Used to flush automatically tables (activated by default)
     */
    public function __destruct()
    {
        if ($this->isAutoFlush()) {
            foreach ($this->tables as $table) {
                $table->flush();
            }
        }
    }

    /**
     * Get the list of table names
     * @return array
     */
    public function getTableNames()
    {
        return $this->storage->getTableList($this);
    }

    /**
     * Get the php class used to instanciate tables
     *
     * @return string
     */
    public function getTableClass()
    {
        return $this->tableClass;
    }

    /**
     * Sets the php class used to instanciate tables
     *
     * @param string $class
     * @return Database
     */
    public function setTableClass($class)
    {
        $class = ltrim($class, '\\');
        if (!class_exists($class)) {
            Exception::throwUnknownClassException($class);
        }
        $reflection = new \ReflectionClass($class);
        if ($class != 'Arnapou\PFDB\Table' && !$reflection->isSubclassOf('Arnapou\PFDB\Table')) {
            Exception::throwInvalidTableClassException($class);
        }
        $this->tableClass = $class;
        return $this;
    }

    /**
     * Get the storage object used to load/store/delete tables
     *
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get a table by its name.
     *
     * It is auto-created if the table doesn't exists
     *
     * @param string $name
     * @return Table
     */
    public function getTable($name)
    {
        if (!isset($this->tables[$name])) {
            $tableClass          = $this->getTableClass();
            $this->tables[$name] = new $tableClass($this, $name);
        }
        return $this->tables[$name];
    }

    /**
     * Drop a table by its name
     *
     * @param string $name
     * @return Database
     */
    public function dropTable($name)
    {
        $this->getTable($name)->drop();
        return $this;
    }

    /**
     * Drop all tables of the database
     *
     * @return Database
     */
    public function drop()
    {
        foreach ($this->tables as $table) {
            $table->drop();
        }
        $this->storage->destroyDatabase($this);
        $this->tables = [];
        return $this;
    }

    /**
     * Tells chether autoflush is activated (true by default)
     *
     * @return bool
     */
    public function isAutoFlush()
    {
        return $this->autoFlush;
    }

    /**
     * Sets the autoflush option
     *
     * @param bool $bool
     * @return Database
     */
    public function setAutoFlush($bool)
    {
        $this->autoFlush = ($bool == true);
        return $this;
    }
}
