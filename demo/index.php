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

require __DIR__ . '/src/bootstrap.php';

(new Page(__FILE__))(
    static function () {
        $source = file_get_contents(__DIR__ . '/../README.md');
        $Parsedown = new Parsedown();

        echo $Parsedown->text($source);
    }
);
