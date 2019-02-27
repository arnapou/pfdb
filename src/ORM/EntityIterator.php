<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\ORM;

class EntityIterator extends \Arnapou\PFDB\Iterator\ConditionIterator
{
    /**
     *
     * @var Table
     */
    private $table;

    /**
     *
     * @param Table     $table
     * @param \Iterator $iterator
     * @param mixed     $condition
     */
    public function __construct(Table $table, \Iterator $iterator, $condition = null)
    {
        $this->table = $table;
        parent::__construct($iterator, $condition);
    }

    /**
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    public function current()
    {
        $array = parent::current();
        if ($array instanceof BaseEntity) {
            return $array;
        }
        $array['id'] = $this->key();
        return $this->table->arrayToEntity($array);
    }
}
