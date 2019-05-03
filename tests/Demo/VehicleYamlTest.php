<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Tests\Demo;

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\Database;
use Arnapou\PFDB\Storage\YamlFileStorage;

class VehicleYamlTest extends VehiclePhpTest
{
    protected function database()
    {
        if (!$this->database) {
            $storage        = new YamlFileStorage(__DIR__ . '/../../demo/database');
            $this->database = new Database($storage);
            $this->database->setAutoFlush(false);
        }
        return $this->database;
    }
}
