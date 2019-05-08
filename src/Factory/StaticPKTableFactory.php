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

use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Storage\StorageInterface;
use Arnapou\PFDB\Table;

class StaticPKTableFactory extends AbstractTableFactory
{
    /**
     * @var string|null
     */
    private $defaultPrimaryKey;

    public function __construct(?string $defaultPrimaryKey = 'id')
    {
        $this->defaultPrimaryKey = $defaultPrimaryKey;
        $this->setTableClass(Table::class);
    }

    public function getDefaultPrimaryKey(): ?string
    {
        return $this->defaultPrimaryKey;
    }

    public function setDefaultPrimaryKey(?string $defaultPrimaryKey): self
    {
        $this->defaultPrimaryKey = $defaultPrimaryKey;
        return $this;
    }

    public function create(StorageInterface $storage, string $name): TableInterface
    {
        $class = $this->getTableClass();
        return new $class($storage, $name, $this->defaultPrimaryKey);
    }
}
