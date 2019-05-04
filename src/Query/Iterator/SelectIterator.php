<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Iterator;

use Arnapou\PFDB\Query\Field\FieldSelectInterface;
use Iterator;

class SelectIterator implements Iterator
{
    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var array
     */
    private $select;

    public function __construct(Iterator $iterator, array $select)
    {
        $this->iterator = $iterator;
        $this->select   = $select;
    }

    public function current()
    {
        $row = $this->iterator->current();
        $key = $this->iterator->key();
        if (!$this->select) {
            return $row;
        }
        $data = [];
        foreach ($this->select as $field) {
            if ($field === '*') {
                $data = array_merge($data, $row);
            } elseif ($field instanceof FieldSelectInterface) {
                $data = array_merge($data, $field->select($row, $key));
            } elseif (\is_object($field) && \is_callable($field)) {
                $data = array_merge($data, (array)$field($row, $key));
            } else {
                $data[$field] = $row[$field] ?? null;
            }
        }
        return $data;
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }
}
