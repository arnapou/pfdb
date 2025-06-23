<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <me@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Storage;

class ArrayStorage implements StorageInterface
{
    /**
     * @param array<string, array<array<mixed>>> $tables
     */
    public function __construct(private array $tables = [])
    {
    }

    public function load(string $name): array
    {
        return $this->tables[$name] ?? [];
    }

    public function save(string $name, array $data): void
    {
        $this->tables[$name] = $data;
    }

    public function isReadonly(string $name): bool
    {
        return false;
    }

    public function delete(string $name): void
    {
        unset($this->tables[$name]);
    }

    public function tableNames(): array
    {
        return array_keys($this->tables);
    }
}
