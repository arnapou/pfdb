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

    /**
     * @return self
     */
    public function setReadonly(bool $readonly);

    public function find(ExprInterface ...$exprs): Query;

    /**
     * @param int|string $id
     */
    public function get($id): ?array;

    public function getName(): string;

    public function getPrimaryKey(): ?string;

    public function getData(): array;

    /**
     * @param int|string|null $id
     *
     * @return self
     */
    public function delete($id);

    /**
     * @return scalar
     */
    public function getLastInsertedKey();

    /**
     * @param ?null|int|string $key
     *
     * @return self
     */
    public function update(array $value, $key = null);

    /**
     * @param ?null|int|string $key
     *
     * @return self
     */
    public function insert(array $value, $key = null);

    /**
     * @param ?null|int|string $key
     *
     * @return self
     */
    public function upsert(array $value, $key = null);

    /**
     * @return self
     */
    public function insertMultiple(array $rows);

    /**
     * @return self
     */
    public function updateMultiple(ExprInterface $expr, callable $function);

    /**
     * @return self
     */
    public function deleteMultiple(ExprInterface $expr);

    public function flush(): bool;

    /**
     * @return self
     */
    public function clear();
}
