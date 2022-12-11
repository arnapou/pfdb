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

namespace Arnapou\PFDB\Core;

use Arnapou\PFDB\Query\Expr\ExprInterface;
use Arnapou\PFDB\Query\Query;
use Countable;
use Traversable;

/**
 * Generic Table Interface.
 *
 * @template-extends Traversable<array-key, array>
 */
interface TableInterface extends Traversable, Countable
{
    /**
     * Whether the table is readonly by itself or whether it is the storage which is readonly.
     */
    public function isReadonly(): bool;

    /**
     * Force this table as readonly to avoid any write.
     *
     * This is a "soft" readonly. The "hard" readonly his own by the storage itself if it supports it.
     */
    public function setReadonly(bool $readonly): self;

    /**
     * Find rows.
     *
     * Return a Query objech which is an iterator you can modify on the fly.
     */
    public function find(ExprInterface ...$exprs): Query;

    /**
     * Get a row based on it's key.
     */
    public function get(int|string $key): ?array;

    /**
     * Return the current table name.
     */
    public function getName(): string;

    /**
     * Return the primary key field name.
     */
    public function getPrimaryKey(): ?string;

    /**
     * Return all data as raw array.
     */
    public function getData(): array;

    /**
     * Delete one row based on a key.
     */
    public function delete(null|int|string $key): self;

    /**
     * Return last inserted Key.
     */
    public function getLastInsertedKey(): string|int|null;

    /**
     * Update one row based on a key.
     */
    public function update(array $row, null|int|string $key = null): self;

    /**
     * Insert one row based on a key.
     */
    public function insert(array $row, null|int|string $key = null): self;

    /**
     * Upsert one row based on a key.
     */
    public function upsert(array $row, null|int|string $key = null): self;

    /**
     * Insert multiple rows selected with an expression.
     */
    public function insertMultiple(array $rows): self;

    /**
     * Updates multiple rows selected with an expression.
     *
     * Pattern of a valid callable function :
     * <pre>
     * function(array $row, int|string|null $key = null): array {
     *     $updatedRow = $row;
     *     // updates
     *     return $updatedRow;
     * }
     * </pre>
     */
    public function updateMultiple(ExprInterface $expr, callable $function): self;

    /**
     * Delete multiple rows selected with an expression.
     */
    public function deleteMultiple(ExprInterface $expr): self;

    /**
     * Save all modifications.
     */
    public function flush(): bool;

    /**
     * Delete all.
     */
    public function clear(): self;
}
