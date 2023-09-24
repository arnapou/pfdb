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

use Arnapou\PFDB\Exception\LockException;

use const LOCK_EX;
use const LOCK_NB;
use const LOCK_UN;

final class FileLocker implements Locker
{
    private readonly string $path;

    /** @var array<string, resource> */
    private array $locks = [];

    public function __construct(string $directoryPathForLocks = null)
    {
        $this->path = $directoryPathForLocks ?? sys_get_temp_dir();

        if (!is_dir($this->path)) {
            throw new LockException("The path $this->path does not exist.");
        }
    }

    public function acquire(string $lockName): bool
    {
        $filename = $this->path . '/php.' . md5($lockName) . '.lock';

        $fp = fopen($filename, 'wb+');
        if (!$fp) {
            return false;
        }

        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            return false;
        }

        $this->locks[":$lockName"] = $fp;

        return true;
    }

    public function release(string $lockName): bool
    {
        if (empty($this->locks[":$lockName"])) {
            return false;
        }

        $fh = $this->locks[":$lockName"];
        @flock($fh, LOCK_UN);
        @fclose($fh);
        unset($this->locks[":$lockName"]);

        return true;
    }

    public function __destruct()
    {
        foreach ($this->locks as $key => $value) {
            $this->release(substr($key, 1));
        }
    }
}
