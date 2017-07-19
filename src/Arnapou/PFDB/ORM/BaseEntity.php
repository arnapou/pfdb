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

use Arnapou\PFDB\Exception\Exception;
use Arnapou\PFDB\Condition\ConditionBuilder;
use Arnapou\PFDB\Condition\ConditionInterface;

class BaseEntity implements \ArrayAccess
{

    /**
     *
     * @var array
     */
    private $__loaded = [];

    /**
     *
     * @var array
     */
    private $__raw;

    /**
     *
     * @var Table
     */
    private $__table;

    /**
     *
     * @var mixed
     */
    private $__id;

    public function __construct()
    {

    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->__id;
    }

    /**
     *
     * @param string $name
     */
    protected function __load($name)
    {
        if (isset($this->__loaded[$name])) {
            return;
        }
        if (!isset($this->__table)) {
            return null;
        }

        $links = $this->__table->getEntity()->getLinks();
        if (!isset($links[$name])) {
            Exception::throwORMException('Link "' . $name . '" cannot be found in schema');
        }
        $target = $links[$name];
        if (Schema\Entity::TYPE_MANY_TO_ONE === $target['type']) {
            if (isset($this->__raw[$target['field']])) {
                $condition   = ConditionBuilder::createAnd()
                    ->equalTo($target['target'], $this->__raw[$target['field']]);
                $this->$name = $this->__table
                    ->getDatabase()
                    ->getTable($target['entity'])
                    ->findOne($condition);
            }
            $this->__loaded[$name] = true;
        } else {
            Exception::throwORMException('Link type "' . $links['type'] . '" is not supported');
        }
    }

    /**
     *
     * @return Table
     */
    protected function __table()
    {
        if (!isset($this->__table)) {
            return null;
        }
        return $this->__table;
    }

    public function offsetExists($offset)
    {
        return $this->offsetGet($offset) !== null;
    }

    public function offsetGet($offset)
    {
        if (!isset($this->__table)) {
            return null;
        }
        if ($offset === 'id') {
            return $this->__id;
        }
        if (false !== strpos($offset, '.')) {
            $offsets = explode('.', $offset);
            $n       = count($offsets);
            $object  = $this;
            while ($n--) {
                $link   = array_shift($offsets);
                $object = $object->offsetGet($link);
                if (!($object instanceof BaseEntity)) {
                    return $object;
                }
            }
            return $object;
        }
        $entity = $this->__table->getEntity();
        // attributes
        $attributes = $entity->getAttributes();
        if (isset($attributes[$offset])) {
            return $this->$offset;
        }
        // links
        $this->__load($offset);
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
        Exception::throwORMException('Set is not supported');
    }

    public function offsetUnset($offset)
    {
        Exception::throwORMException('Unset is not supported');
    }

}