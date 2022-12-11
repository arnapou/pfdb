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

namespace Arnapou\PFDB\Exception;

use TypeError;

final class Expected extends TypeError
{
    public function __construct(string $type, mixed $value)
    {
        parent::__construct(
            "Expected $type, got a " . get_debug_type($value)
        );
    }
}
