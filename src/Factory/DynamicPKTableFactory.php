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
use Arnapou\PFDB\Table;
use Closure;

/**
 * Allow to build a different key for each based on the table name.
 * Default is primary key named like "id<tablename>".
 * You can customize with your own callable.
 *
 * The callable first argument is the tablename
 */
class DynamicPKTableFactory extends AbstractTableFactory
{
    private Closure $pkFactory;

    public function __construct(?callable $pkFactory = null)
    {
        parent::__construct();

        $this->pkFactory = $pkFactory
            ? $pkFactory(...)
            : static fn (string $name): string => "id$name";
    }

    public function getPkFactory(): Closure
    {
        return $this->pkFactory;
    }

    public function setPkFactory(callable $pkFactory): self
    {
        $this->pkFactory = $pkFactory(...);

        return $this;
    }

    public function create(StorageInterface $storage, string $name): TableInterface
    {
        return $this->createInstance($storage, $name, ($this->pkFactory)($name));
    }
}
