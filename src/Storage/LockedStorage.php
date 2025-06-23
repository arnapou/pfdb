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

use Arnapou\Lock\Adapter\FileLocker;
use Arnapou\Lock\Decorator\WaitingLocker;
use Arnapou\Lock\Locker;
use Arnapou\PFDB\Exception\StorageException;

class LockedStorage implements StorageInterface
{
    /** @var array<string, true> */
    private array $locked = [];

    public function __construct(
        private readonly StorageInterface $storage,
        private readonly Locker $locker = new FileLocker(),
    ) {
    }

    private function lock(string $name): void
    {
        if (!\array_key_exists($name, $this->locked)) {
            $waitingLocker = new WaitingLocker($this->locker, 20);

            if (!$waitingLocker->acquire($name)) {
                throw new StorageException('Unable to lock the file within 20 seconds.');
            }

            $this->locked[$name] = true;
        }
    }

    public function __destruct()
    {
        $this->releaseLocks();
    }

    public function releaseLocks(): void
    {
        foreach ($this->locked as $name => $value) {
            $this->locker->release($name);
        }
        $this->locked = [];
    }

    public function load(string $name): array
    {
        $this->lock($name);

        return $this->storage->load($name);
    }

    public function save(string $name, array $data): void
    {
        $this->lock($name);
        $this->storage->save($name, $data);
    }

    public function delete(string $name): void
    {
        $this->lock($name);
        $this->storage->delete($name);
    }

    public function isReadonly(string $name): bool
    {
        $this->lock($name);

        return $this->storage->isReadonly($name);
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
