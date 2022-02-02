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

use Arnapou\PFDB\Exception\DirectoryNotFoundException;
use Arnapou\PFDB\Exception\InvalidTableNameException;
use Arnapou\PFDB\Exception\ReadonlyException;

abstract class AbstractFileStorage implements StorageInterface
{
    private readonly string $path;
    private readonly bool $readonly;

    public function __construct(string $path, private readonly string $prefixName = 'table')
    {
        $this->path = rtrim(rtrim($path, '/'), '\\');
        if (!is_dir($this->path)) {
            throw new DirectoryNotFoundException("path: $path");
        }
        $this->readonly = !is_writable($path);
        if (!$this->isValidTableName($prefixName)) {
            throw new InvalidTableNameException('The prefix name must follow the same rules as table name [a-z0-9_\.-]+');
        }
    }

    public function getFilename(string $name): string
    {
        if (!$this->isValidTableName($name)) {
            throw new InvalidTableNameException('The name must follow this regexp [a-zA-Z0-9_\.-]+');
        }

        return $this->path . '/' . $this->prefixName . ".$name." . $this->getExtension();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isValidTableName(string $name): bool
    {
        return (bool) preg_match('!^[a-zA-Z0-9_\.-]+$!', $name);
    }

    public function tableNames(): array
    {
        $files = glob($this->getPath() . '/' . $this->prefixName . '.*.' . $this->getExtension(), GLOB_NOSORT) ?: [];
        $names = [];
        foreach ($files as $file) {
            $name = substr(basename($file, '.' . $this->getExtension()), \strlen($this->prefixName) + 1);
            if ($this->isValidTableName($name)) {
                $names[] = $name;
            }
        }

        return $names;
    }

    public function delete(string $name): void
    {
        if ($this->isReadonly($name)) {
            throw new ReadonlyException();
        }
        $filename = $this->getFilename($name);
        if (is_file($filename)) {
            unlink($filename);
        }
    }

    public function isReadonly(string $name): bool
    {
        $filename = $this->getFilename($name);
        if (is_file($filename) && !is_writable($filename)) {
            return true;
        }
        if ($this->readonly) {
            return true;
        }

        return false;
    }

    abstract protected function getExtension(): string;
}
