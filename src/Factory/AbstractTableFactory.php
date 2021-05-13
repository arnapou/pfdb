<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Factory;

use Arnapou\PFDB\Core\AbstractTable;
use Arnapou\PFDB\Exception\InvalidTableClassException;

abstract class AbstractTableFactory implements TableFactoryInterface
{
    /**
     * @var string
     */
    private $tableClass;

    public function getTableClass(): string
    {
        return $this->tableClass;
    }

    public function setTableClass(string $tableClass): self
    {
        if (!is_subclass_of($tableClass, AbstractTable::class)) {
            throw new InvalidTableClassException('This factory works with classes child of built-in AbstractTable');
        }
        $this->tableClass = $tableClass;

        return $this;
    }
}
