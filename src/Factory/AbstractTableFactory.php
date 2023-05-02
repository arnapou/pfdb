<?php

declare(strict_types=1);

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
use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Exception\InvalidTableClassException;
use Arnapou\PFDB\Storage\StorageInterface;
use Arnapou\PFDB\Table;

/**
 * All the children classes of this abstract work with an AbstractTable class.
 */
abstract class AbstractTableFactory implements TableFactoryInterface
{
    /** @var class-string<AbstractTable> */
    private string $tableClass;

    public function __construct()
    {
        $this->tableClass = Table::class;
    }

    /**
     * @return class-string<AbstractTable>
     */
    public function getTableClass(): string
    {
        return $this->tableClass;
    }

    /**
     * @param class-string $tableClass
     */
    public function setTableClass(string $tableClass): self
    {
        if (!is_subclass_of($tableClass, AbstractTable::class)) {
            throw new InvalidTableClassException('This factory works with classes child of built-in AbstractTable');
        }

        $this->tableClass = $tableClass;

        return $this;
    }

    protected function createInstance(
        StorageInterface $storage,
        string $name,
        ?string $primaryKey
    ): TableInterface {
        $tableClass = $this->getTableClass();

        return new $tableClass($storage, $name, $primaryKey);
    }
}
