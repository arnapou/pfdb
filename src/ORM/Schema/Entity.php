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

class Entity
{
    const TYPE_MANY_TO_ONE = 'many_to_one';

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $class;

    /**
     *
     * @var array
     */
    protected $attributes = [];

    /**
     *
     * @var array
     */
    protected $links = [];

    /**
     *
     * @var array
     */
    protected static $instances = [];

    /**
     *
     * @param string $name
     * @param string $class
     */
    private function __construct($name, $class)
    {
        $this->name  = $name;
        $this->class = $class;
    }

    /**
     *
     * @param string $name
     * @param string $class
     * @return Entity
     */
    public static function getInstance($name, $class)
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new self($name, $class);
        } elseif ($name !== null && $name !== self::$instances[$class]->getName()) {
            Exception::throwORMException('Conflict between two entities with same class and different names.');
        }
        return self::$instances[$class];
    }

    /**
     *
     * @param string $name
     * @param string $entity
     * @param string $field  local field
     * @param string $target target field
     * @return Entity
     */
    public function addLinkManyToOne($name, $entity, $field = null, $target = null)
    {
        if (isset($this->attributes[$name])) {
            Exception::throwFatalException('Cannot add link "' . $name . '" : it already exists in attributes.');
        }
        $this->links[$name] = [
            'type'   => self::TYPE_MANY_TO_ONE,
            'entity' => $entity,
            'field'  => ($field === null ? $name . '_id' : $field),
            'target' => $target,
        ];
        return $this;
    }

    /**
     *
     * @param string $name
     * @param string $type
     * @return Entity
     */
    public function addAttribute($name, $type = 'string', $length = null, $nullable = true)
    {
        if (isset($this->links[$name])) {
            Exception::throwFatalException('Cannot add attribute "' . $name . '" : it already exists in links.');
        }
        $this->attributes[$name] = [
            'type'     => $type,
            'length'   => $length,
            'nullable' => $nullable,
        ];
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'name'       => $this->name,
            'class'      => $this->class,
            'attributes' => $this->attributes,
            'links'      => $this->links,
        ];
        return $array;
    }

    /**
     *
     * @param array $array
     * @return Entity
     */
    public static function fromArray($array)
    {
        if (!isset($array['name'])) {
            Exception::throwORMException('Cannot find "name" key.');
        }
        if (!isset($array['class'])) {
            Exception::throwORMException('Cannot find "class" key.');
        }
        $entity = self::getInstance($array['name'], $array['class']);
        if (isset($array['attributes']) && \is_array($array['attributes'])) {
            foreach ($array['attributes'] as $name => $value) {
                $type     = $value['type'] ?? 'string';
                $length   = $value['length'] ?? null;
                $nullable = $value['nullable'] ?? true;
                $entity->addAttribute($name, $type, $length, $nullable);
            }
        }
        if (isset($array['links']) && \is_array($array['links'])) {
            foreach ($array['links'] as $name => $value) {
                if (\is_array($value) && isset($value['type'])) {
                    if (self::TYPE_MANY_TO_ONE === $value['type']) {
                        if (!isset($value['entity'])) {
                            Exception::throwORMException('Cannot find "entity" key in link.');
                        }
                        if (!isset($value['field'])) {
                            Exception::throwORMException('Cannot find "field" key in link.');
                        }
                        $target = $value['target'] ?? null;
                        $entity->addLinkManyToOne($name, $value['entity'], $value['field'], $target);
                    }
                }
            }
        }
        return $entity;
    }
}
