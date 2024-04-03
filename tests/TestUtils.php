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

namespace Arnapou\PFDB\Tests;

class TestUtils
{
    public static function inGitlabCI(): bool
    {
        $env = getenv('CI_JOB_NAME');

        return \is_string($env) && '' !== $env;
    }
}
