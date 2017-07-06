<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\ORM;

use Arnapou\PFDB\Storage\StorageInterface;

class Database extends \Arnapou\PFDB\Database
{

    /**
     *
     * @var array
     */
    protected $defaultConfig = [
        'autoflush' => true,
        'tableclass' => 'Arnapou\PFDB\ORM\Table',
    ];

    /**
     *
     * @var Schema\Schema
     */
    protected $schema;

    public function __construct(StorageInterface $storage, Schema\Schema $schema, $config = [])
    {
        $this->schema = $schema;
        parent::__construct($storage, $config);
    }

    /**
     *
     * @return Schema\Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

}