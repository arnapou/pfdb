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

/**
 * Table Factory Interface.
 */
interface TableFactoryInterface
{
    /**
     * Create a table.
     */
    public function create(StorageInterface $storage, string $name): TableInterface;
}
