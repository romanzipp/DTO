<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Flexible;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Tests\Support\FlexibleData;

class FlexibleValuesTest extends TestCase
{
    public function testClassIsDetectedAsFlexible()
    {
        self::assertTrue(
            FlexibleData::isFlexible()
        );
    }

    public function testNotFlexibleFailing()
    {
        $this->expectException(InvalidDataException::class);

        new class(['foo' => 'bar']) extends AbstractData {
        };
    }

    public function testNotFlexibleFailingMultiple()
    {
        $this->expectException(InvalidDataException::class);

        new class(['foo' => 'bar', 'bar' => 'foo']) extends AbstractData {
        };
    }

    public function testOverloading()
    {
        $data = new #[Flexible] class(['foo' => 'bar']) extends AbstractData
        {
        };

        self::assertSame('bar', $data->foo);
    }

    public function testOverloadingWithExisting()
    {
        $data = new #[Flexible] class(['foo' => 'bar', 'bar' => 'foo']) extends AbstractData
        {
            public string $bar;
        };

        self::assertSame('bar', $data->foo);
        self::assertSame('foo', $data->bar);
    }
}
