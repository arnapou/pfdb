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

namespace Arnapou\PFDB\Tests\Core;

use Arnapou\PFDB\Core\Assert;
use Arnapou\PFDB\Exception\Expected;
use Generator;

use function get_class;

use PHPUnit\Framework\TestCase;
use Stringable;

class AssertTest extends TestCase
{
    /**
     * @return Generator<string, array<mixed, callable>>
     */
    public static function dataOk(): Generator
    {
        yield 'positiveInt(1)' => [1, fn () => Assert::positiveInt(1)];
        yield 'isInt(1)' => [1, fn () => Assert::isInt(1)];
        yield 'isIntStringNull(1)' => [1, fn () => Assert::isIntStringNull(1)];
        yield 'isIntStringNull(null)' => [null, fn () => Assert::isIntStringNull(null)];
        yield 'isIntStringNull("foo")' => ['foo', fn () => Assert::isIntStringNull('foo')];
        yield 'isArray([])' => [[], fn () => Assert::isArray([])];
        yield 'isScalar(true)' => [true, fn () => Assert::isScalar(true)];
        yield 'isScalar(1)' => [1, fn () => Assert::isScalar(1)];
        yield 'isScalar(3.14)' => [3.14, fn () => Assert::isScalar(3.14)];
        yield 'isScalar("foo")' => ['foo', fn () => Assert::isScalar('foo')];
        yield 'isScalar(null)' => [null, fn () => Assert::isScalar(null)];
        yield 'isString("string")' => ['string', fn () => Assert::isString('string')];
        yield 'isString("stringable")' => [
            'stringable',
            fn () => Assert::isString(
                new class() implements Stringable {
                    public function __toString(): string
                    {
                        return 'stringable';
                    }
                }
            ),
        ];
        yield 'isString("__toString")' => [
            '__toString',
            fn () => Assert::isString(
                new class() {
                    public function __toString(): string
                    {
                        return '__toString';
                    }
                }
            ),
        ];
    }

    /**
     * @return Generator<string, array<mixed, callable>>
     */
    public static function dataFail(): Generator
    {
        yield 'positiveInt(0)' => [
            new Expected('positive int', 0),
            fn () => Assert::positiveInt(0),
        ];
        yield 'isInt(3.14)' => [
            new Expected('int', 3.14),
            fn () => Assert::isInt(3.14),
        ];
        yield 'isIntStringNull(3.14)' => [
            new Expected('int, string or null', 3.14),
            fn () => Assert::isIntStringNull(3.14),
        ];
        yield 'isArray(3.14)' => [
            new Expected('array', 3.14),
            fn () => Assert::isArray(3.14),
        ];
        yield 'isScalar([])' => [
            new Expected('scalar', []),
            fn () => Assert::isScalar([]),
        ];
        yield 'isString([])' => [
            new Expected('stringable', []),
            fn () => Assert::isString([]),
        ];
    }

    /**
     * @dataProvider dataOk
     */
    public function testAssertOk(mixed $expected, callable $test): void
    {
        self::assertSame($expected, $test());
    }

    /**
     * @dataProvider dataFail
     */
    public function testAssertFail(Expected $expected, callable $test): void
    {
        try {
            $test();
            $this->fail('Not expected success for "' . $expected->getMessage() . '"');
        } catch (Expected $error) {
            self::assertSame(get_class($expected), get_class($error));
            self::assertSame($expected->getMessage(), $error->getMessage());
            self::assertSame($expected->getCode(), $error->getCode());
        }
    }
}
