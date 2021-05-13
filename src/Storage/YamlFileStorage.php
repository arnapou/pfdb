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

use Arnapou\PFDB\Exception\ReadonlyException;
use Symfony\Component\Yaml\Yaml;

class YamlFileStorage extends AbstractFileStorage
{
    /**
     * @var int
     */
    private $dumpInline = 2;
    /**
     * @var int
     */
    private $dumpIndent = 2;
    /**
     * @var int
     */
    private $dumpFlags = Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;
    /**
     * @var int
     */
    private $parseFlags = 0;

    protected function getExtension(): string
    {
        return 'yaml';
    }

    public function load(string $name): array
    {
        $filename = $this->getFilename($name);
        if (is_file($filename)) {
            return Yaml::parseFile($filename, $this->parseFlags);
        }

        return [];
    }

    public function save(string $name, array $data): void
    {
        if ($this->isReadonly($name)) {
            throw new ReadonlyException();
        }
        file_put_contents(
            $this->getFilename($name),
            Yaml::dump($data, $this->dumpInline, $this->dumpIndent, $this->dumpFlags),
            LOCK_EX
        );
    }

    public function getDumpInline(): int
    {
        return $this->dumpInline;
    }

    public function setDumpInline(int $inline): self
    {
        $this->dumpInline = $inline;

        return $this;
    }

    public function getDumpIndent(): int
    {
        return $this->dumpIndent;
    }

    public function setDumpIndent(int $indent): self
    {
        $this->dumpIndent = $indent;

        return $this;
    }

    public function getDumpFlags(): int
    {
        return $this->dumpFlags;
    }

    public function setDumpFlags(int $flags): self
    {
        $this->dumpFlags = $flags;

        return $this;
    }

    public function getParseFlags(): int
    {
        return $this->parseFlags;
    }

    public function setParseFlags(int $flags): self
    {
        $this->parseFlags = $flags;

        return $this;
    }
}
