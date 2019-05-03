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

interface StorageInterface
{
    public function load(string $name): array;

    public function save(string $name, array $data): void;

    public function delete(string $name): void;

    public function isReadonly(string $name): bool;

    public function tableNames(): array;
}
