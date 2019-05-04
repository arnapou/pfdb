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

use Symfony\Component\Yaml\Yaml;

class YamlFileStorage extends AbstractFileStorage
{
    protected function getExtension(): string
    {
        return 'yaml';
    }

    public function load(string $name): array
    {
        $filename = $this->getFilename($name);
        if (is_file($filename)) {
            return Yaml::parseFile($filename);
        }
        return [];
    }

    public function save(string $name, array $data): void
    {
        file_put_contents(
            $this->getFilename($name),
            Yaml::dump($data, 2, 2),
            LOCK_EX
        );
    }
}
