<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Cases\CamelCase;
use romanzipp\DTO\Cases\KebabCase;
use romanzipp\DTO\Cases\PascalCase;
use romanzipp\DTO\Cases\SnakeCase;

class ToArrayTest extends TestCase
{
    public function testToArray()
    {
        $data = new class extends AbstractData {
            public string $firstAttribute = '1';
            public string $second_attribute = '2';
        };

        self::assertSame([
            'firstAttribute' => '1',
            'second_attribute' => '2',
        ], $data->toArray());
    }

    public function testToArrayFlexible()
    {
        $data = new class(['thirdAttribute' => '3']) extends AbstractData {
            protected static bool $flexible = true;
            public string $firstAttribute = '1';
            public string $second_attribute = '2';
        };

        self::assertSame([
            'firstAttribute' => '1',
            'second_attribute' => '2',
            'thirdAttribute' => '3',
        ], $data->toArray());
    }

    public function testPascalCase()
    {
        $data = new class extends AbstractData {
            public string $firstAttribute = '1';
            public string $second_attribute = '2';
        };

        self::assertSame([
            'FirstAttribute' => '1',
            'SecondAttribute' => '2',
        ], $data->toArray(PascalCase::class));
    }

    public function testCamelCase()
    {
        $data = new class extends AbstractData {
            public string $firstAttribute = '1';
            public string $second_attribute = '2';
        };

        self::assertSame([
            'firstAttribute' => '1',
            'secondAttribute' => '2',
        ], $data->toArray(CamelCase::class));
    }

    public function testSnakeCase()
    {
        $data = new class extends AbstractData {
            public string $firstAttribute = '1';
            public string $second_attribute = '2';
        };

        self::assertSame([
            'first_attribute' => '1',
            'second_attribute' => '2',
        ], $data->toArray(SnakeCase::class));
    }

    public function testKebabCase()
    {
        $data = new class extends AbstractData {
            public string $firstAttribute = '1';
            public string $second_attribute = '2';
        };

        self::assertSame([
            'first-attribute' => '1',
            'second-attribute' => '2',
        ], $data->toArray(KebabCase::class));
    }
}
