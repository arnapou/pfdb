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

namespace Arnapou\PFDB\Storage;

use Arnapou\Ensure\Ensure;
use Arnapou\PFDB\Exception\ReadonlyException;
use RuntimeException;
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
            return $this->yamlParse($filename);
        }

        return [];
    }

    /**
     * @return array<array<mixed>>
     */
    protected function yamlParse(string $filename): array
    {
        /** @phpstan-ignore-next-line Yes, this is an array of arrays... */
        return Ensure::array(
            match (true) {
                \function_exists('yaml_parse_file') => yaml_parse_file($filename),
                class_exists(Yaml::class) => Yaml::parseFile($filename),
                default => throw new RuntimeException('You need the yaml extension or symfony/yaml to use this YamlFileStorage'),
            }
        );
    }

    /**
     * @param array<array<mixed>> $data
     */
    protected function yamlDump(array $data): string
    {
        return match (true) {
            \function_exists('yaml_emit') => yaml_emit($data),
            class_exists(Yaml::class) => Yaml::dump($data, indent: 2, flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK),
            default => throw new RuntimeException('You need the yaml extension or symfony/yaml to use this YamlFileStorage'),
        };
    }

    public function save(string $name, array $data): void
    {
        if ($this->isReadonly($name)) {
            throw new ReadonlyException();
        }
        file_put_contents($this->getFilename($name), $this->yamlDump($data), LOCK_EX);
    }
}
