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

class CachedFileStorage implements StorageInterface
{
    private readonly PhpFileStorage $cache;

    public function __construct(
        private readonly AbstractFileStorage $storage,
        string $cachePath,
        string $cachedPrefixName = 'cached',
    ) {
        $this->cache = new PhpFileStorage($cachePath, $cachedPrefixName);
    }

    public function load(string $name): array
    {
        $filename = $this->storage->getFilename($name);
        if (!is_file($filename)) {
            $this->cache->delete($name);

            return [];
        }

        $cachename = $this->cache->getFilename($name);
        if (!is_file($cachename) || filemtime($filename) >= filemtime($cachename)) {
            $data = $this->storage->load($name);
            $this->cache->save($name, $data);

            return $data;
        }

        return $this->cache->load($name);
    }

    public function save(string $name, array $data): void
    {
        $this->storage->save($name, $data);
        $this->cache->save($name, $data);
    }

    public function isReadonly(string $name): bool
    {
        return $this->storage->isReadonly($name);
    }

    public function delete(string $name): void
    {
        $this->storage->delete($name);
        $this->cache->delete($name);
    }

    public function tableNames(): array
    {
        return $this->storage->tableNames();
    }

    public function innerStorage(): AbstractFileStorage
    {
        return $this->storage;
    }

    public function cacheStorage(): PhpFileStorage
    {
        return $this->cache;
    }
}
