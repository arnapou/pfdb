<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Iterator;

class ArrayIterator implements \Iterator, \Countable
{
    use TraitIterator;

    protected $array;
    protected $count;
    protected $key;
    protected $current;

    public function __construct(&$array)
    {
        $this->array = &$array;
        $this->count = \count($array);
    }

    public function count()
    {
        return $this->count;
    }

    public function current()
    {
        return $this->current;
    }

    public function key()
    {
        return $this->key;
    }

    public function next()
    {
        next($this->array);
    }

    public function rewind()
    {
        $this->key     = null;
        $this->current = null;
        reset($this->array);
    }

    public function valid()
    {
        $this->key = key($this->array);
        if ($this->key === null || $this->key === false) {
            $this->current = null;
            return false;
        }
        $this->current = current($this->array);
        return true;
    }
}
