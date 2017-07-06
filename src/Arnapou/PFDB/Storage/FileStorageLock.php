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

class FileStorageLock
{

    /**
     *
     * @var string
     */
    protected $filename;

    /**
     *
     * @var int
     */
    protected $fileHandle;

    /**
     *
     * @var boolean
     */
    protected $locked;

    /**
     *
     * @var boolean
     */
    protected $isWin;

    /**
     *
     * @var int
     */
    protected $waitLoopDuration = 50; // ms

    /**
     *
     * @var bool
     */
    protected $hasWaited = false;

    /**
     *
     * @param string $lockfile
     */
    public function __construct($filename)
    {
        if (!is_file($filename)) {
            throw new Exception('Filename does not exists');
        }
        $this->isWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->filename = $filename;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->unlock();
    }

    /**
     * Tells whether it is locked
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Tells whether the object waited for the lock
     * @return bool
     */
    public function hasWaited()
    {
        return $this->hasWaited;
    }

    /**
     *
     * @param int $maxWaitTime max wait time in milliseconds (0 for infinite)
     * @return bool tells whether it succeded or not
     */
    public function waitUntilLocked($maxWaitTime = 0)
    {
        $waitStep = 1000 * $this->waitLoopDuration;
        $this->hasWaited = false;
        if ($maxWaitTime <= 0) {
            // infinite wait
            while (!$this->lock()) {
                usleep($waitStep);
                $this->hasWaited = true;
            }
        } else {
            // limited wait
            $wait = 0;
            while (!$this->lock()) {
                usleep($waitStep);
                $this->hasWaited = true;
                $wait += $waitStep;
                if ($wait > 1000 * $maxWaitTime) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     *
     * @return int duration in milliseconds
     */
    public function getWaitLoopDuration()
    {
        return $this->waitLoopDuration;
    }

    /**
     *
     * @param int $duration in milliseconds
     * @return LockFile
     */
    public function setWaitLoopDuration($duration)
    {
        $this->waitLoopDuration = $duration;
        return $this;
    }

    /**
     * Try to lock and return if it success
     * @return bool
     */
    public function lock()
    {
        if ($this->locked) {
            return false;
        }
        if ($this->isWin) {
            if (false !== $lock = @fopen($this->filename . '.lock', 'x')) {
                fclose($lock);
                $this->locked = true;
            }
        } else {
            try {
                $this->fileHandle = fopen($this->filename, 'rb');
                $this->locked = flock($this->fileHandle, LOCK_EX | LOCK_NB);
                if (!$this->locked) {
                    @fclose($this->fileHandle);
                }
            } catch (\Exception $e) {
                if ($this->fileHandle) {
                    @fclose($this->fileHandle);
                }
                $this->locked = false;
            }
        }
        return $this->locked;
    }

    /**
     * unlock
     */
    public function unlock()
    {
        if ($this->locked) {
            if ($this->isWin) {
                if (is_file($this->filename . '.lock')) {
                    @unlink($this->filename . '.lock');
                }
            } elseif (is_resource($this->fileHandle)) {
                flock($this->fileHandle, LOCK_UN);
                @fclose($this->fileHandle);
            }
            $this->fileHandle = null;
            $this->locked = false;
            return true;
        }
        return false;
    }

}
