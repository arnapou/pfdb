<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/vendor/autoload.php';

//$data = include(__DIR__ . '/demo/database/table.vehicle.php');
//echo Yaml::dump($data);




$yaml = file_get_contents(__DIR__ . '/demo/database/table.vehicle.yaml');
echo Yaml::dump(Yaml::parse($yaml));



echo "\n";
