<?php

namespace romanzipp\DTO\Tests;

use Closure;
use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Tests\Support\SimpleData;
use romanzipp\DTO\Tests\Support\SimpleDataNullable;
use romanzipp\DTO\Tests\Support\SimpleDataNullableDefaultNull;
use romanzipp\DTO\Tests\Support\SimpleDataNullableRequired;
use romanzipp\DTO\Tests\Support\SimpleDataRequired;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHinted;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHintedRequired;
use romanzipp\DTO\Tests\Support\SimpleDataTypeUnion;

class ValuesTest extends TestCase
{
    public function testArrayValues()
    {
        $data = new class(['array' => []]) extends AbstractData {
            public array $array;
        };

        self::assertSame([], $data->array);
    }

    public function testBooleanValues()
    {
        $data = new class(['bool' => true]) extends AbstractData {
            public bool $bool;
        };

        self::assertSame(true, $data->bool);
    }

    public function testBooleanValuesMustBeStrict()
    {
        $this->expectException(InvalidDataException::class);

        new class(['bool' => 1]) extends AbstractData {
            public bool $bool;
        };
    }

    public function testIntValues()
    {
        $data = new class(['int' => 1]) extends AbstractData {
            public int $int;
        };

        self::assertSame(1, $data->int);
    }

    public function testIntValuesMustBeStrict()
    {
        $this->expectException(InvalidDataException::class);

        new class(['int' => '1']) extends AbstractData {
            public int $int;
        };
    }

    public function testFloatValues()
    {
        $data = new class(['float' => 1.0]) extends AbstractData {
            public float $float;
        };

        self::assertSame(1.0, $data->float);
    }

    public function testFloatValuesMustBeStrict()
    {
        $this->expectException(InvalidDataException::class);

        new class(['float' => '1.0']) extends AbstractData {
            public float $float;
        };
    }

    public function testClosureValues()
    {
        $data = new class(['callback' => fn () => null]) extends AbstractData {
            public Closure $callback;
        };

        self::assertIsCallable($data->callback);
    }

    public function testObjectValues()
    {
        $object = new class() {
        };

        $data = new class(['object' => $object]) extends AbstractData {
            public object $object;
        };

        self::assertIsObject($data->object);
    }

    public function testValues()
    {
        $data = new SimpleData([]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleData([
            'foo' => null,
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleData([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testValuesRequired()
    {
        $data = new SimpleDataRequired([
            'foo' => null,
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleDataRequired([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testValuesTypeHinted()
    {
        $data = new SimpleDataTypeHinted([]);

        self::assertFalse($data->isset('foo'));

        $data = new SimpleDataTypeHinted([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testValuesTypeHintedRequired()
    {
        $data = new SimpleDataTypeHintedRequired([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testValuesTypeUnion()
    {
        $data = new SimpleDataTypeUnion([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);

        $data = new SimpleDataTypeUnion([
            'foo' => 1,
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(1, $data->foo);
    }

    public function testValuesNullable()
    {
        $data = new SimpleDataNullable([]);

        self::assertFalse($data->isset('foo'));

        $data = new SimpleDataNullable([
            'foo' => null,
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleDataNullable([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testValuesNullableRequired()
    {
        $data = new SimpleDataNullableRequired([
            'foo' => null,
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleDataNullableRequired([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testValuesNullableDefaultNull()
    {
        $data = new SimpleDataNullableDefaultNull([]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleDataNullableDefaultNull([
            'foo' => null,
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame(null, $data->foo);

        $data = new SimpleDataNullableDefaultNull([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }
}
