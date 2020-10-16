<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Property;
use romanzipp\DTO\Tests\Support\SimpleData;
use romanzipp\DTO\Tests\Support\SimpleDataNullable;
use romanzipp\DTO\Tests\Support\SimpleDataNullableDefaultNull;
use romanzipp\DTO\Tests\Support\SimpleDataNullableDefaultNullRequired;
use romanzipp\DTO\Tests\Support\SimpleDataNullableRequired;
use romanzipp\DTO\Tests\Support\SimpleDataRequired;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHinted;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHintedRequired;
use romanzipp\DTO\Values\MissingValue;

class ValidationTest extends TestCase
{
    public function testSimpleData()
    {
        $property = Property::collectFromClass(SimpleData::class)['foo'];

        self::assertValid($property, '');
        self::assertValid($property, 1);
        self::assertValid($property, null);
        self::assertValid($property, new MissingValue());
    }

    public function testSimpleDataRequired()
    {
        $property = Property::collectFromClass(SimpleDataRequired::class)['foo'];

        self::assertValid($property, '');
        self::assertValid($property, 1);
        self::assertValid($property, null);
        self::assertInvalid($property, new MissingValue());
    }

    public function testSimpleDataTypeHinted()
    {
        $property = Property::collectFromClass(SimpleDataTypeHinted::class)['foo'];

        self::assertValid($property, '');
        self::assertInvalid($property, 1);
        self::assertInvalid($property, null);
        self::assertValid($property, new MissingValue());
    }

    public function testSimpleDataTypeHintedRequired()
    {
        $property = Property::collectFromClass(SimpleDataTypeHintedRequired::class)['foo'];

        self::assertValid($property, '');
        self::assertInvalid($property, 1);
        self::assertInvalid($property, null);
        self::assertInvalid($property, new MissingValue());
    }

    public function testSimpleDataNullable()
    {
        $property = Property::collectFromClass(SimpleDataNullable::class)['foo'];

        self::assertValid($property, '');
        self::assertInvalid($property, 1);
        self::assertValid($property, null);
        self::assertValid($property, new MissingValue());
    }

    public function testSimpleDataNullableRequired()
    {
        $property = Property::collectFromClass(SimpleDataNullableRequired::class)['foo'];

        self::assertValid($property, '');
        self::assertInvalid($property, 1);
        self::assertValid($property, null);
        self::assertInvalid($property, new MissingValue());
    }

    public function testSimpleDataNullableDefaultNull()
    {
        $property = Property::collectFromClass(SimpleDataNullableDefaultNull::class)['foo'];

        self::assertValid($property, '');
        self::assertInvalid($property, 1);
        self::assertValid($property, null);
        self::assertValid($property, new MissingValue());
    }

    public function testSimpleDataNullableDefaultNullRequired()
    {
        $this->expectException(InvalidDeclarationException::class);

        Property::collectFromInstance(new SimpleDataNullableDefaultNullRequired())['foo'];
    }
}
