<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Exceptions\InvalidDataException;

class FlexibleValuesTest extends TestCase
{
    public function testNotFlexibleFailing()
    {
        $this->expectException(InvalidDataException::class);

        new class(['foo' => 'bar']) extends AbstractData {
            protected static bool $flexible = false;
        };
    }

    public function testNotFlexibleFailingMultiple()
    {
        $this->expectException(InvalidDataException::class);

        new class(['foo' => 'bar', 'bar' => 'foo']) extends AbstractData {
            protected static bool $flexible = false;
        };
    }

    public function testOverloading()
    {
        $data = new class(['foo' => 'bar']) extends AbstractData {
            protected static bool $flexible = true;
        };

        self::assertSame('bar', $data->foo);
    }

    public function testOverloadingWithExisting()
    {
        $data = new class(['foo' => 'bar', 'bar' => 'foo']) extends AbstractData {
            protected static bool $flexible = true;
            public string $bar;
        };

        self::assertSame('bar', $data->foo);
        self::assertSame('foo', $data->bar);
    }
}
