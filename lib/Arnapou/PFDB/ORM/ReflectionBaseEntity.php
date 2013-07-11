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

class ReflectionBaseEntity extends \ReflectionClass {

	/**
	 *
	 * @var \ReflectionProperty
	 */
	protected $propertyTable;

	/**
	 *
	 * @var \ReflectionProperty
	 */
	protected $propertyId;

	/**
	 *
	 * @var \ReflectionProperty
	 */
	protected $propertyRaw;

	/**
	 *
	 * @var \ReflectionMethod
	 */
	protected $methodLoad;

	public function __construct() {
		parent::__construct('Arnapou\PFDB\ORM\BaseEntity');

		$this->propertyId = $this->getProperty('__id');
		$this->propertyId->setAccessible(true);

		$this->propertyTable = $this->getProperty('__table');
		$this->propertyTable->setAccessible(true);

		$this->propertyRaw = $this->getProperty('__raw');
		$this->propertyRaw->setAccessible(true);

		$this->methodLoad = $this->getMethod('__load');
		$this->methodLoad->setAccessible(true);
	}

	/**
	 * 
	 * @return \ReflectionMethod
	 */
	public function getMethodLoad() {
		return $this->methodLoad;
	}

	/**
	 * 
	 * @return \ReflectionProperty
	 */
	public function getPropertyTable() {
		return $this->propertyTable;
	}

	/**
	 * 
	 * @return \ReflectionProperty
	 */
	public function getPropertyId() {
		return $this->propertyId;
	}

	/**
	 * 
	 * @return \ReflectionProperty
	 */
	public function getPropertyRaw() {
		return $this->propertyRaw;
	}

}