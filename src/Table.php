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

use Arnapou\PFDB\Exception\PrimaryKeyNotFoundException;
use Arnapou\PFDB\Storage\StorageInterface;
use Traversable;

class Table implements \IteratorAggregate
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

    public function __construct(string $name, StorageInterface $storage, ?string $primaryKey = null)
    {
        $this->storage    = $storage;
        $this->name       = $name;
        $this->primaryKey = $primaryKey;
        $this->load();
    }

    private function load()
    {
        $rows = $this->storage->load($this->name);
        if ($this->primaryKey) {
            $this->data = [];
            foreach ($rows as $row) {
                if (!\array_key_exists($this->primaryKey, $row)) {
                    throw new PrimaryKeyNotFoundException();
                }
                $this->data[$row[$this->primaryKey]] = $row;
            }
        } else {
            $this->data = $rows;
        }
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
