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

use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Storage\StorageInterface;

class StaticPKTableFactory extends AbstractTableFactory
{
    public function __construct(
        private ?string $defaultPrimaryKey = 'id',
    ) {
        parent::__construct();
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
        return $this->createInstance($storage, $name, $this->defaultPrimaryKey);
    }
}
