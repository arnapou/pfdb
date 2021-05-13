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

use Arnapou\PFDB\Exception\StorageException;

class MultipleStorage implements StorageInterface
{
    /**
     * @var StorageInterface[]
     */
    private $storages = [];

    public function __construct(StorageInterface ...$storages)
    {
        $this->storages = $storages;
    }

    public function add(StorageInterface $storage): self
    {
        $this->storages[] = $storage;

        return $this;
    }

    public function findChild(string $name): ?StorageInterface
    {
        foreach ($this->storages as $storage) {
            if (\in_array($name, $storage->tableNames())) {
                return $storage;
            }
        }

        return null;
    }

    public function load(string $name): array
    {
        $storage = $this->findChild($name);

        return $storage ? $storage->load($name) : [];
    }

    public function save(string $name, array $data): void
    {
        if ($storage = $this->findChild($name)) {
            $storage->save($name, $data);
        } elseif (isset($this->storages[0])) {
            $this->storages[0]->save($name, $data);
        } else {
            throw new StorageException('No storage found to save the table');
        }
    }

    public function isReadonly(string $name): bool
    {
        $storage = $this->findChild($name);

        return $storage ? $storage->isReadonly($name) : false;
    }

    public function delete(string $name): void
    {
        if ($storage = $this->findChild($name)) {
            $storage->delete($name);
            unset($this->storages[$name]);
        } else {
            throw new StorageException('No storage found to delete the table');
        }
    }

    public function tableNames(): array
    {
        $names = [];
        foreach ($this->storages as $storage) {
            $names[] = $storage->tableNames();
        }

        return array_merge([], ...$names);
    }

    /**
     * @return StorageInterface[]
     */
    public function children(): array
    {
        return $this->storages;
    }
}
