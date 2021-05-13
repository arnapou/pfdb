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

use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class LockedStorage implements StorageInterface
{
    /**
     * @var Lock
     */
    private $lockFactory;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var Lock[]
     */
    private $locks = [];
    /**
     * @var string
     */
    private $lockNamePrefix;

    public function __construct(StorageInterface $storage, ?LockFactory $lockFactory = null, string $lockNamePrefix = 'pfdb.')
    {
        $this->storage = $storage;
        $this->lockFactory = $lockFactory ?: new LockFactory(new FlockStore());
        $this->lockNamePrefix = $lockNamePrefix;
    }

    private function lock(string $name): void
    {
        if (!\array_key_exists($name, $this->locks)) {
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
