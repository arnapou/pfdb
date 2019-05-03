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

use Arnapou\PFDB\Core\StorageInterface;
use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Exception\PrimaryKeyNotFoundException;
use Traversable;

class Table implements TableInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var array
     */
    private $data = null;
    /**
     * @var bool
     */
    private $changed = false;
    /**
     * @var string
     */
    private $primaryKey = null;

    public function __construct(string $name, StorageInterface $storage, ?string $primaryKey = 'id')
    {
        $this->storage    = $storage;
        $this->name       = $name;
        $this->primaryKey = $primaryKey;
        $this->load();
    }

    private function load()
    {
        $data = [];
        if (null === $this->primaryKey) {
            foreach ($this->storage->load($this->name) as $row) {
                $data[] = $row;
            }
        } else {
            foreach ($this->storage->load($this->name) as $row) {
                if (!\array_key_exists($this->primaryKey, $row)) {
                    throw new PrimaryKeyNotFoundException();
                }
                $data[$row[$this->primaryKey]] = $row;
            }
        }
        $this->data    = $data;
        $this->changed = false;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
}
