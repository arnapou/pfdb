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

class FileStorageLock {

	/**
	 *
	 * @var string
	 */
	protected $lockfile = '';

	/**
	 *
	 * @var bool
	 */
	protected $ownLock = false;

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
	public function __construct($lockfile) {
		$this->lockfile = $lockfile . '.lock';
		register_shutdown_function(array($this, 'unlock'));
	}

	/**
	 * Tells whether it is locked
	 * @return bool
	 */
	public function isLocked() {
		return is_file($this->lockfile);
	}

	/**
	 * Tells whether this object own the lock
	 * @return bool
	 */
	public function ownLock() {
		return $this->ownLock;
	}

	/**
	 * Tells whether the object waited for the lock
	 * @return bool
	 */
	public function hasWaited() {
		return $this->hasWaited;
	}

	/**
	 *
	 * @param int $maxWaitTime max wait time in milliseconds (0 for infinite)
	 * @return bool tells whether it succeded or not
	 */
	public function waitUntilLocked($maxWaitTime = 0) {
		$waitStep = 1000 * $this->waitLoopDuration;
		$this->hasWaited = false;
		if ( $maxWaitTime <= 0 ) {
			// infinite wait
			while ( !$this->lock() ) {
				usleep($waitStep);
				$this->hasWaited = true;
			}
		}
		else {
			// limited wait
			$wait = 0;
			while ( !$this->lock() ) {
				usleep($waitStep);
				$this->hasWaited = true;
				$wait += $waitStep;
				if ( $wait > 1000 * $maxWaitTime ) {
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
	public function getWaitLoopDuration() {
		return $this->waitLoopDuration;
	}

	/**
	 *
	 * @param int $duration in milliseconds
	 * @return LockFile 
	 */
	public function setWaitLoopDuration($duration) {
		$this->waitLoopDuration = $duration;
		return $this;
	}

	/**
	 * Try to lock and return if it success
	 * @return bool
	 */
	public function lock() {
		if ( false !== $lock = @fopen($this->lockfile, 'x') ) {
			$this->ownLock = true;
			fclose($lock);
			return true;
		}
		return false;
	}

	/**
	 * unlock
	 */
	public function unlock() {
		if ( is_file($this->lockfile) ) {
			@unlink($this->lockfile);
		}
		$this->ownLock = false;
	}

}