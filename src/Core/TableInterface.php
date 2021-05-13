<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Core;

use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Query;
use Countable;

interface TableInterface extends \Traversable, Countable
{
    public function isReadonly(): bool;

    public function setReadonly(bool $readonly);

    public function find(ExprInterface ...$exprs): Query;

    public function get($id): ?array;

    public function getName(): string;

    public function getPrimaryKey(): ?string;

    public function getData(): array;

    public function delete($id);

    public function getLastInsertedKey();

    public function update(array $value, $key = null);

    public function insert(array $value, $key = null);

    public function upsert(array $value, $key = null);

    public function insertMultiple(array $rows);

    public function updateMultiple(ExprInterface $expr, callable $function);

    public function deleteMultiple(ExprInterface $expr);

    public function flush(): bool;

    public function clear();
}
