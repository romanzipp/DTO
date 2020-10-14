<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\Attribute;
use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Tests\Support\SimpleData;
use romanzipp\DTO\Tests\Support\SimpleDataNullable;
use romanzipp\DTO\Tests\Support\SimpleDataNullableDefaultNull;
use romanzipp\DTO\Tests\Support\SimpleDataNullableDefaultNullRequired;
use romanzipp\DTO\Tests\Support\SimpleDataNullableRequired;
use romanzipp\DTO\Tests\Support\SimpleDataRequired;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHinted;
use romanzipp\DTO\Tests\Support\SimpleDataTypeHintedRequired;
use romanzipp\DTO\Values\MissingValue;

class BasicTest extends TestCase
{
    public function testSimpleData()
    {
        $attribute = Attribute::collectFromClass(SimpleData::class)['foo'];

        self::assertValid($attribute, '');
        self::assertValid($attribute, 1);
        self::assertValid($attribute, null);
        self::assertValid($attribute, new MissingValue());
    }

    public function testSimpleDataRequired()
    {
        $attribute = Attribute::collectFromClass(SimpleDataRequired::class)['foo'];

        self::assertValid($attribute, '');
        self::assertValid($attribute, 1);
        self::assertValid($attribute, null);
        self::assertInvalid($attribute, new MissingValue());
    }

    public function testSimpleDataTypeHinted()
    {
        $attribute = Attribute::collectFromClass(SimpleDataTypeHinted::class)['foo'];

        self::assertValid($attribute, '');
        self::assertInvalid($attribute, 1);
        self::assertInvalid($attribute, null);
        self::assertInvalid($attribute, new MissingValue());
    }

    public function testSimpleDataTypeHintedRequired()
    {
        $attribute = Attribute::collectFromClass(SimpleDataTypeHintedRequired::class)['foo'];

        self::assertValid($attribute, '');
        self::assertInvalid($attribute, 1);
        self::assertInvalid($attribute, null);
        self::assertInvalid($attribute, new MissingValue());
    }

    public function testSimpleDataNullable()
    {
        $attribute = Attribute::collectFromClass(SimpleDataNullable::class)['foo'];

        self::assertValid($attribute, '');
        self::assertInvalid($attribute, 1);
        self::assertValid($attribute, null);
        self::assertInvalid($attribute, new MissingValue());
    }

    public function testSimpleDataNullableRequired()
    {
        $attribute = Attribute::collectFromClass(SimpleDataNullableRequired::class)['foo'];

        self::assertValid($attribute, '');
        self::assertInvalid($attribute, 1);
        self::assertValid($attribute, null);
        self::assertInvalid($attribute, new MissingValue());
    }

    public function testSimpleDataNullableDefaultNull()
    {
        $attribute = Attribute::collectFromClass(SimpleDataNullableDefaultNull::class)['foo'];

        self::assertValid($attribute, '');
        self::assertInvalid($attribute, 1);
        self::assertValid($attribute, null);
        self::assertValid($attribute, new MissingValue());
    }

    public function testSimpleDataNullableDefaultNullRequired()
    {
        $this->expectException(InvalidDeclarationException::class);

        $attribute = Attribute::collectFromClass(SimpleDataNullableDefaultNullRequired::class)['foo'];
    }
}
