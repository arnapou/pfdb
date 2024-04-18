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

namespace Arnapou\PFDB;

use Arnapou\PFDB\Core\AbstractDatabase;
use Arnapou\PFDB\Factory\StaticPKTableFactory;
use Arnapou\PFDB\Factory\TableFactoryInterface;
use Arnapou\PFDB\Storage\ReadonlyStorage;
use Arnapou\PFDB\Storage\StorageInterface;

final class DatabaseReadonly extends AbstractDatabase
{
    public function __construct(
        StorageInterface $storage,
        bool $quiet = true,
        TableFactoryInterface $tableFactory = new StaticPKTableFactory('id'),
    ) {
        parent::__construct(new ReadonlyStorage($storage, $quiet), $tableFactory);
    }
}
