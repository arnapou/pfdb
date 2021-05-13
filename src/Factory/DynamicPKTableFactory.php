<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Factory;

use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Storage\StorageInterface;
use Arnapou\PFDB\Table;

/**
 * Allow to build a different key for each based on the table name.
 * Default is primary key named like "id<tablename>".
 * You can customize with your own callable.
 *
 * The callable first argument is the tablename
 */
class DynamicPKTableFactory extends AbstractTableFactory
{
    /**
     * @var callable|null
     */
    private $pkFactory;

    public function __construct(?callable $pkFactory = null)
    {
        $this->pkFactory = $pkFactory ?: function ($name) {
            return "id$name";
        };
        $this->setTableClass(Table::class);
    }

    public function getPkFactory(): callable
    {
        return $this->pkFactory;
    }

    public function setPkFactory(callable $pkFactory): self
    {
        $this->pkFactory = $pkFactory;

        return $this;
    }

    public function create(StorageInterface $storage, string $name): TableInterface
    {
        $class = $this->getTableClass();

        return new $class($storage, $name, \call_user_func($this->pkFactory, $name));
    }
}
