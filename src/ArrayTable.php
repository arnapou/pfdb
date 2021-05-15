<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB;

use Arnapou\PFDB\Core\TableDecorator;
use Arnapou\PFDB\Storage\ArrayStorage;

/**
 * This table is only here as a simple "helper" to use a simple array in a Table object.
 *
 * If you just want to iterate/find/sort/... things on an array without any modification,
 * it is probably better to just use the Query object with the array passed as an ArrayIterator
 * ie: $query = new \Arnapou\PFDB\Query\Query(new \ArrayIterator($yourArray))
 */
final class ArrayTable extends TableDecorator
{
    public const NAME = 'array';

    public function __construct(
        array $data,
        ?string $primaryKey
    ) {
        parent::__construct(
            new Table(
                new ArrayStorage([self::NAME => $data]),
                self::NAME,
                $primaryKey
            )
        );
    }
}
