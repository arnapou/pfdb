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

namespace Arnapou\PFDB\Storage;

use function array_key_exists;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;

class LockedStorage implements StorageInterface
{
    private readonly LockFactory $lockFactory;
    /**
     * @var array<string, LockInterface>
     */
    private array $locks = [];

    public function __construct(
        private readonly StorageInterface $storage,
        LockFactory $lockFactory = null,
        private readonly string $lockNamePrefix = 'pfdb.'
    ) {
        $this->lockFactory = $lockFactory ?: new LockFactory(new FlockStore());
    }

    private function lock(string $name): void
    {
        if (!array_key_exists($name, $this->locks)) {
            $lock = $this->lockFactory->createLock($this->lockNamePrefix . $name);
            $lock->acquire(true);
            $this->locks[$name] = $lock;
        }
    }

    public function __destruct()
    {
        $this->releaseLocks();
    }

    public function releaseLocks(): void
    {
        foreach ($this->locks as $lock) {
            $lock->release();
        }
        $this->locks = [];
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
