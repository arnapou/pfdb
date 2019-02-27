<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Exception;

use Arnapou\PFDB\Table;

class Exception extends \Exception
{
    /**
     *
     * @param string $type
     */
    public static function throwBadArgumentTypeException($type)
    {
        throw new BadArgumentTypeException("Argument type error, expected : $type");
    }

    /**
     *
     * @param string $type
     */
    public static function throwORMException($message)
    {
        throw new ORMException($message);
    }

    /**
     *
     * @param string $type
     */
    public static function throwFatalException($message)
    {
        throw new FatalException($message);
    }

    /**
     *
     * @param string $directory
     */
    public static function throwDirectoryNotFoundException($directory)
    {
        throw new DirectoryNotFoundException("Directory not found '$directory'");
    }

    /**
     *
     * @param string $directory
     */
    public static function throwDirectoryNotWritableException($directory)
    {
        throw new DirectoryNotWritableException("Directory not writable '$directory'");
    }

    /**
     *
     * @param string $name
     */
    public static function throwInvalidTableNameException($name)
    {
        throw new InvalidTableNameException("Invalid table name '$name'");
    }

    /**
     *
     * @param Table $table
     */
    public static function throwLockedTableException($table)
    {
        throw new LockedTableException("Table '" . $table->getName() . "' is locked.");
    }

    /**
     *
     * @param Table $table
     */
    public static function throwInvalidTableDataException($table)
    {
        throw new InvalidTableDataException("Invalid table data (maybe corrupted) '" . $table->getName() . "'");
    }

    /**
     *
     * @param string $class
     */
    public static function throwInvalidTableClassException($class)
    {
        throw new InvalidTableClassException("Invalid table class '" . $class . "'");
    }

    /**
     *
     * @param string $class
     */
    public static function throwUnknownClassException($class)
    {
        throw new UnknownClassException("Unknown class '" . $class . "'");
    }

    /**
     *
     * @param string $operator
     */
    public static function throwUnknownOperatorException($operator)
    {
        throw new UnknownOperatorException("Unknown operator '" . $operator . "'");
    }

    /**
     *
     * @param string $message
     */
    public static function throwInvalidConditionSyntaxException($message)
    {
        throw new InvalidConditionSyntaxException('Condition syntax error : ' . $message);
    }

    
    public static function throwInvalidRootOperatorException()
    {
        throw new InvalidRootOperatorException('Root operator should be either AND or OR.');
    }

    /**
     *
     * @param string $key
     */
    public static function throwArrayKeyNotFoundException($key)
    {
        throw new ArrayKeyNotFoundException("Array key '$key' not found.");
    }
}
