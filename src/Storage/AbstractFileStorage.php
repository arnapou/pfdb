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

abstract class AbstractFileStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var bool
     */
    private $readonly;
    /**
     * @var bool
     */
    private $flushed;

    public function __construct($path)
    {
        $this->path = rtrim(rtrim($path, '/'), '\\');
        if (!is_dir($this->path)) {
            throw new DirectoryNotFoundException();
        }
        $this->readonly = !is_writable($path);
    }

    protected function getFilename(string $name): string
    {
        if (!$this->isValidTableName($name)) {
            throw new InvalidTableNameException();
        }
        return $this->path . "/table.$name." . $this->getExtension();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function isValidTableName(string $name): bool
    {
        return preg_match('!^[a-z0-9_\.-]+$!', $name);
    }

    public function tableNames(): array
    {
        $files = glob($this->getPath() . '/table.*.' . $this->getExtension(), GLOB_NOSORT) ?: [];
        $names = [];
        foreach ($files as $file) {
            $name = str_replace('table.', '', basename($file, '.' . $this->getExtension()));
            if ($this->isValidTableName($name)) {
                $names[] = $name;
            }
        }
        return $names;
    }

    public function delete(string $name): void
    {
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
