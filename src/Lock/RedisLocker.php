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

namespace Arnapou\PFDB\Lock;

use Redis;

final class RedisLocker implements Locker
{
    /** @var array<string, string> */
    private array $locks = [];

    public function __construct(
        private readonly Redis $redis,
        private readonly bool $autorelease = true,
        private readonly int $defaultTtl = 300,
        private readonly string $namespace = 'locks:',
    ) {
    }

    public function acquire(string $lockName): bool
    {
        $rediskey = $this->namespace . md5($lockName);

        if (!$this->redis->set($rediskey, $lockName, ['nx', 'ex' => $this->defaultTtl])) {
            return false;
        }

        $this->locks[":$lockName"] = $rediskey;

        return true;
    }

    public function release(string $lockName): bool
    {
        if (!empty($this->locks[":$lockName"])) {
            $this->redis->del($this->locks[":$lockName"]);
            unset($this->locks[":$lockName"]);
        }

        return true;
    }

    public function __destruct()
    {
        if ($this->autorelease) {
            foreach ($this->locks as $key => $value) {
                $this->release(substr($key, 1));
            }
        }
    }
}
