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
    public static function throwBadArgumentTypeException(string $type)
    {
        throw new BadArgumentTypeException("Argument type error, expected : $type");
    }

    public static function throwFatalException(string $message)
    {
        throw new FatalException($message);
    }

    public static function throwDirectoryNotFoundException(string $directory)
    {
        throw new DirectoryNotFoundException("Directory not found '$directory'");
    }

    public static function throwDirectoryNotWritableException(string $directory)
    {
        throw new DirectoryNotWritableException("Directory not writable '$directory'");
    }

    public static function throwInvalidTableNameException(string $name)
    {
        throw new InvalidTableNameException("Invalid table name '$name'");
    }

    public static function throwLockedTableException(Table $table)
    {
        throw new LockedTableException("Table '" . $table->getName() . "' is locked.");
    }

    public static function throwInvalidTableDataException(Table $table)
    {
        throw new InvalidTableDataException("Invalid table data (maybe corrupted) '" . $table->getName() . "'");
    }

    public static function throwInvalidTableClassException(string $class)
    {
        throw new InvalidTableClassException("Invalid table class '" . $class . "'");
    }

    public static function throwUnknownClassException(string $class)
    {
        throw new UnknownClassException("Unknown class '" . $class . "'");
    }

    public static function throwUnknownOperatorException(string $operator)
    {
        throw new UnknownOperatorException("Unknown operator '" . $operator . "'");
    }

    public static function throwInvalidConditionSyntaxException(string $message)
    {
        throw new InvalidConditionSyntaxException('Condition syntax error : ' . $message);
    }

    public static function throwInvalidRootOperatorException()
    {
        throw new InvalidRootOperatorException('Root operator should be either AND or OR.');
    }

    public static function throwArrayKeyNotFoundException(string $key)
    {
        throw new ArrayKeyNotFoundException("Array key '$key' not found.");
    }
}
