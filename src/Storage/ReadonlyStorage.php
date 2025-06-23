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

use Arnapou\PFDB\Exception\ReadonlyException;

class ReadonlyStorage implements StorageInterface
{
    /**
     * ReadonlyStorage constructor.
     *
     * @param bool $quiet No Readonly exception will be thrown if set to true
     */
    public function __construct(private readonly StorageInterface $storage, private readonly bool $quiet = true)
    {
    }

    public function load(string $name): array
    {
        return $this->storage->load($name);
    }

    public function save(string $name, array $data): void
    {
        if (!$this->quiet && $this->storage->isReadonly($name)) {
            throw new ReadonlyException();
        }
        // nothing to do: that's the purpose
    }

    public function delete(string $name): void
    {
        if (!$this->quiet && $this->storage->isReadonly($name)) {
            throw new ReadonlyException();
        }
        // nothing to do: that's the purpose
    }

    public function isReadonly(string $name): bool
    {
        if (!$this->quiet) {
            return $this->storage->isReadonly($name);
        }

        return false;
    }

    public function tableNames(): array
    {
        return $this->storage->tableNames();
    }

    public function innerStorage(): StorageInterface
    {
        return $this->storage;
    }
}
