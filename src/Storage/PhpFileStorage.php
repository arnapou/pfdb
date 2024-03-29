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

use Arnapou\PFDB\Exception\ReadonlyException;

class PhpFileStorage extends AbstractFileStorage
{
    protected function getExtension(): string
    {
        return 'php';
    }

    public function load(string $name): array
    {
        $filename = $this->getFilename($name);
        if (is_file($filename)) {
            return include $filename;
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
            '<?php return ' . var_export($data, true) . ";\n",
            LOCK_EX
        );
    }
}
