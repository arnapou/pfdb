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

namespace Arnapou\PFDB\Lock\Decorator;

use Arnapou\PFDB\Lock\Locker;

final readonly class WaitingLocker implements Locker
{
    /**
     * @param positive-int $minLoopWaitMilliseconds
     * @param positive-int $maxLoopWaitMilliseconds
     */
    public function __construct(
        private Locker $internal,
        private float $maxTotalWaitSeconds,
        private int $minLoopWaitMilliseconds = 20,
        private int $maxLoopWaitMilliseconds = 150,
    ) {
    }

    public function acquire(string $lockName): bool
    {
        $startTime = microtime(true);
        $loopWait = max($this->minLoopWaitMilliseconds, 5);

        while (true) {
            if ($acquired = $this->internal->acquire($lockName)) {
                break;
            }

            if (microtime(true) - $startTime > $this->maxTotalWaitSeconds) {
                break;
            }

            usleep(1000 * $loopWait);

            // Increase the wait time at each iteration to avoid too much loops.
            $loopWait = min(2 * $loopWait, $this->maxLoopWaitMilliseconds);
        }

        return $acquired;
    }

    public function release(string $lockName): bool
    {
        return $this->internal->release($lockName);
    }
}
