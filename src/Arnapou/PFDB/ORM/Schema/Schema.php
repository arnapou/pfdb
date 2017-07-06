<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\ORM\Schema;

use Arnapou\PFDB\Exception\Exception;
use Arnapou\PFDB\ORM\BaseEntity;

class Schema
{

    /**
     *
     * @var array
     */
    protected $entities = [];

    public function __construct()
    {

    }

    /**
     *
     * @param string $name
     * @param string $class
     * @return Entity
     */
    public function addEntity($name, $class)
    {
        if ($name instanceof Entity) {
            $entity = $name;
            $name = $entity->getName();
            $class = $entity->getClass();
        } else {
            $entity = new Entity($name, $class);
        }
        if (isset($this->entities[$name])) {
            Exception::throwORMException('Entity "' . $name . '" is already defined.');
        }
        $this->entities[$name] = $entity;
        return $entity;
    }

    /**
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        $entities = [];
        foreach ($entities as $entity) {
            $entities[] = $entity->toArray();
        }
        return [
            'entities' => $entities,
        ];
    }

    /**
     *
     * @param string $name
     * @return Entity
     */
    public function getEntity($name)
    {
        if (!isset($this->entities[$name])) {
            Exception::throwORMException('Cannot find entity "' . $name . '".');
        }
        return $this->entities[$name];
    }

    /**
     *
     * @param array $array
     * @return Schema
     */
    static public function fromArray($array)
    {
        if (!isset($array['entities'])) {
            Exception::throwORMException('Cannot find entities key in array.');
        }
        if (!is_array($array['entities'])) {
            Exception::throwORMException('entities key is not an array.');
        }
        $schema = new self();
        foreach ($array['entities'] as $entity) {
            if (!isset($entity['class'])) {
                Exception::throwORMException('Cannot find class key in array.');
            }
            if (!isset($entity['name'])) {
                Exception::throwORMException('Cannot find name key in array.');
            }
            $schema->addEntity(Entity::fromArray($entity), null);
        }
        return $schema;
    }

}