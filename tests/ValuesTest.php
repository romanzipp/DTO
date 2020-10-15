<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\Tests\Support\SimpleData;
use romanzipp\DTO\Tests\Support\SimpleDataNullable;
use romanzipp\DTO\Tests\Support\SimpleDataNullableDefaultNull;
use romanzipp\DTO\Tests\Support\SimpleDataNullableRequired;
use romanzipp\DTO\Tests\Support\SimpleDataRequired;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHinted;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHintedRequired;

class ValuesTest extends TestCase
{
    public function testSimpleData()
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

    public function testSimpleDataRequired()
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

    public function testSimpleDataTypeHinted()
    {
        $data = new SimpleDataTypeHinted([]);

        self::assertFalse($data->isset('foo'));
        // self::assertSame('bar', $data->foo);

        $data = new SimpleDataTypeHinted([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testSimpleDataTypeHintedRequired()
    {
        $data = new SimpleDataTypeHintedRequired([
            'foo' => 'bar',
        ]);

        self::assertTrue($data->isset('foo'));
        self::assertSame('bar', $data->foo);
    }

    public function testSimpleDataNullable()
    {
        $data = new SimpleDataNullable([]);

        self::assertFalse($data->isset('foo'));
        // self::assertSame(null, $data->foo);

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

    public function testSimpleDataNullableRequired()
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

    public function testSimpleDataNullableDefaultNull()
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
