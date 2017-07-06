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

use Arnapou\PFDB\Condition\ConditionInterface;
use Arnapou\PFDB\Exception\Exception;

class ConditionIterator extends \FilterIterator implements \Countable
{

    use TraitIterator;

    /**
     *
     * @var ConditionInterface
     */
    protected $condition = null;

    /**
     *
     * @param \Iterator          $iterator
     * @param ConditionInterface $condition
     */
    public function __construct($iterator, $condition = null)
    {
        if (!($iterator instanceof \Iterator)) {
            Exception::throwInvalidConditionSyntaxException("iterator is not a valid php iterator");
        }
        if ($condition !== null && !($condition instanceof ConditionInterface)) {
            Exception::throwInvalidConditionSyntaxException("condition is not a valid Arnapou\PFDB\Condition\ConditionInterface");
        }
        $this->condition = $condition;
        parent::__construct($iterator);
    }

    public function count()
    {
        return iterator_count($this);
    }

    public function accept()
    {
        if ($this->condition === null) {
            return true;
        } else {
            return $this->condition->match($this->key(), $this->current());
        }
    }

}
