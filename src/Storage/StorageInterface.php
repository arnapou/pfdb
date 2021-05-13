<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Storage;

interface StorageInterface
{
    /**
     * Used by Table object when it is instanciated.
     */
    public function load(string $name): array;

    /**
     * Used by Table object when the flush is triggered.
     */
    public function save(string $name, array $data): void;

    /**
     * Used by Table object.
     * This is the real source readonly flag, not the Table readonly flag which avoid the table flush.
     *
     * If you dont care writing, just return true.
     */
    public function isReadonly(string $name): bool;

    /**
     * Use by Database object, no need to implement if you directly use Table objects.
     */
    public function delete(string $name): void;

    /**
     * Use by Database object, no need to implement if you directly use Table objects.
     */
    public function tableNames(): array;
}
