<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Field;

class KeyField implements FieldInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(?string $name = null)
    {
        $this->name = null === $name ? ':key' : $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(array $row, $key = null)
    {
        return $key;
    }

    public function select(array $row, $key = null): array
    {
        return [$this->name => $key];
    }
}
