<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Flexible;
use romanzipp\DTO\Cases\CamelCase;
use romanzipp\DTO\Cases\KebabCase;
use romanzipp\DTO\Cases\PascalCase;
use romanzipp\DTO\Cases\SnakeCase;

class ToArrayTest extends TestCase
{
    public function testToArray()
    {
        $data = new class() extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'firstProperty' => '1',
            'second_property' => '2',
        ], $data->toArray());
    }

    public function testToArrayFlexible()
    {
        $data = new #[Flexible] class(['thirdProperty' => '3']) extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'firstProperty' => '1',
            'second_property' => '2',
            'thirdProperty' => '3',
        ], $data->toArray());
    }

    public function testToArrayNestedObjectsInArray()
    {
        $data = new class(['children' => [new class(['text' => 'foo']) extends AbstractData {
            public string $text;
        }]]) extends AbstractData {
            public array $children = [];
        };

        self::assertSame(['children' => [['text' => 'foo']]], $data->toArray());
    }

    public function testToArrayNestedObjectsInArrayDeep()
    {
        $data = new class(['children' => [new class(['children' => [new class(['text' => 'foo']) extends AbstractData {
            public string $text;
        }]]) extends AbstractData {
            public array $children = [];
        }]]) extends AbstractData {
            public array $children = [];
        };

        self::assertSame(['children' => [['children' => [['text' => 'foo']]]]], $data->toArray());
    }

    public function testToArrayNestedObjectsInArrayDeepConvertedKeyCase()
    {
        $data = new class(['more_children' => [new class(['someChildren' => [new class(['text' => 'foo']) extends AbstractData {
            public string $text;
        }]]) extends AbstractData {
            public array $someChildren = [];
        }]]) extends AbstractData {
            public array $more_children = [];
        };

        self::assertSame(['more_children' => [['some_children' => [['text' => 'foo']]]]], $data->toArrayConverted(SnakeCase::class));
    }

    public function testFilteredStaticProperties()
    {
        $data = new class([]) extends AbstractData {
            protected static string $staticProperty = '1';
            public string $scopedProperty = '2';
        };

        self::assertSame([
            'scopedProperty' => '2',
        ], $data->toArray());
    }

    public function testConvertedPascalCase()
    {
        $data = new class() extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'FirstProperty' => '1',
            'SecondProperty' => '2',
        ], $data->toArrayConverted(PascalCase::class));
    }

    public function testConvertedCamelCase()
    {
        $data = new class() extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'firstProperty' => '1',
            'secondProperty' => '2',
        ], $data->toArrayConverted(CamelCase::class));
    }

    public function testConvertedSnakeCase()
    {
        $data = new class() extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'first_property' => '1',
            'second_property' => '2',
        ], $data->toArrayConverted(SnakeCase::class));
    }

    public function testConvertedKebabCase()
    {
        $data = new class() extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'first-property' => '1',
            'second-property' => '2',
        ], $data->toArrayConverted(KebabCase::class));
    }

    public function testConvertedInvalidClassName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given case formatter `romanzipp\DTO\Tests\ToArrayTest` is invalid');

        $data = new class() extends AbstractData {
            public string $firstProperty = '1';
            public string $second_property = '2';
        };

        self::assertSame([
            'first-property' => '1',
            'second-property' => '2',
        ], $data->toArrayConverted(self::class));
    }
}
