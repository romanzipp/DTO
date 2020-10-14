<?php

namespace romanzipp\DTO\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use romanzipp\DTO\Attribute;

abstract class TestCase extends BaseTestCase
{
    protected static function assertValid(Attribute $attribute, $value)
    {
        self::assertTrue($attribute->isValid($value), ($error = $attribute->getError($value)) ? $error->getMessage() : '');
    }

    protected static function assertInvalid(Attribute $attribute, $value)
    {
        self::assertFalse($attribute->isValid($value));
    }
}
