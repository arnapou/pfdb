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

class Table extends \Arnapou\PFDB\Table
{

    /**
     *
     * @var Schema\Entity
     */
    protected $entity;

    /**
     *
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    /**
     *
     * @var ReflectionBaseEntity
     */
    protected $reflectionBaseEntity;

    public function __construct(Database $database, $name)
    {
        parent::__construct($database, $name);
        $this->entity = $database->getSchema()->getEntity($name);
        $this->reflectionClass = new \ReflectionClass($this->entity->getClass());
        $this->reflectionBaseEntity = new ReflectionBaseEntity();
    }

    /**
     *
     * @return Schema\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function getIterator()
    {
        return new EntityIterator($this, parent::getIterator());
    }

    public function newEntity()
    {
        return $this->reflectionClass->newInstance();
    }

    public function save(BaseEntity $object)
    {
        $this->set(null, $object);
        return $this;
    }

    public function set($key, $object)
    {
        $array = $this->entityToArray($object);
        $id = $array['id'];
        unset($array['id']);
        if ($key === null) {
            if ($id !== null) {
                parent::set($id, $array);
                return $id;
            } else {
                $id = parent::set(null, $array);
                $this->reflectionBaseEntity->getPropertyId()->setValue($object, $id);
                $this->reflectionBaseEntity->getPropertyTable()->setValue($object, $this);
                return $id;
            }
        } else {
            if ($id !== null) {
                if ($id !== $key) {
                    Exception::throwORMException('Key conflict.');
                }
                return parent::set($id, $array);
            } else {
                $this->reflectionBaseEntity->getPropertyId()->setValue($object, $key);
                $this->reflectionBaseEntity->getPropertyTable()->setValue($object, $this);
                return parent::set($key, $array);
            }
        }
    }

    public function get($key)
    {
        $array = parent::get($key);
        if ($array === null) {
            return null;
        }
        return $this->arrayToEntity($array);
    }

    public function update($condition, $callable)
    {
        if (!is_callable($callable)) {
            Exception::throwBadArgumentTypeException('callable');
        }
        $iterator = $this->getIterator()->find($condition);
        foreach ($iterator as $key => $row) {
            $this->offsetSet($key, call_user_func($callable, $this->entity->arrayToObject($row)));
        }
        return $this;
    }

    /**
     *
     * @param BaseEntity $object
     * @param int        $maxDepth
     * @return array
     */
    public function entityToArray(BaseEntity $object, $maxDepth = 0)
    {
        $class = $this->entity->getClass();
        if (!($object instanceof $class)) {
            Exception::throwORMException('Invalid entity class ' . get_class($object));
        }

        $array = [];
        $reflectionObject = new \ReflectionObject($object);

        // id
        $array['id'] = $object->getId();

        // attributes
        foreach ($this->entity->getAttributes() as $name => $attribute) {
            $property = $reflectionObject->getProperty($name);
            $property->setAccessible(true);
            $array[$name] = $property->getValue($object);
        }

        // links
        foreach ($this->entity->getLinks() as $name => $link) {
            $property = $reflectionObject->getProperty($name);
            $property->setAccessible(true);
            $this->reflectionBaseEntity->getMethodLoad()->invoke($object, $name);
            $targetObject = $property->getValue($object);

            if (Schema\Entity::TYPE_MANY_TO_ONE === $link['type']) {
                $targetTable = $this->getDatabase()->getTable($link['entity']);
                $entityClass = $targetTable->getEntity()->getClass();
                if ($targetObject === null) {
                    $value = null;
                } elseif ($targetObject instanceof $entityClass) {
                    if ($link['target'] === null) {
                        $value = $targetObject->getId();
                    } else {
                        $targetReflectionObject = new \ReflectionObject($targetObject);
                        $targetProperty = $targetReflectionObject->getProperty($link['target']);
                        $targetProperty->setAccessible(true);
                        $value = $targetProperty->getValue($targetObject);
                    }
                } else {
                    Exception::throwORMException('linked object is not a valid ' . $entityClass . ' object');
                }
                $array[$link['field']] = $value;
                if ($maxDepth > 0) {
                    if ($targetObject === null) {
                        $array[$name] = null;
                    } else {
                        $array[$name] = $targetTable->entityToArray($targetObject, $maxDepth - 1);
                    }
                }
            }
        }

        return $array;
    }

    /**
     *
     * @param array      $array
     * @param BaseEntity $entity
     * @return BaseEntity
     */
    public function arrayToEntity(array $array, BaseEntity $entity = null)
    {

        $objectId = isset($array['id']) ? $array['id'] : null;
        if ($entity === null) {
            $entity = $this->newEntity();
        }
        $reflectionObject = new \ReflectionObject($entity);

        $this->reflectionBaseEntity->getPropertyId()->setValue($entity, $objectId);
        $this->reflectionBaseEntity->getPropertyTable()->setValue($entity, $this);
        $this->reflectionBaseEntity->getPropertyRaw()->setValue($entity, $array);

        // attributes
        foreach ($this->entity->getAttributes() as $name => $attribute) {
            if (array_key_exists($name, $array)) {
                $property = $reflectionObject->getProperty($name);
                $property->setAccessible(true);
                $property->setValue($entity, $array[$name]);
            }
        }

        return $entity;
    }

}