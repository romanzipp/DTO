<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\AbstractData;
use stdClass;

class ToJsonTest extends TestCase
{
    public function testToJson()
    {
        $data = new class(['foo' => 'bar']) extends AbstractData {
            public string $foo;
        };

        self::assertEquals('{"foo":"bar"}', $data->toJson());
    }

    public function testToJsonObject()
    {
        $data = new class(['foo' => (object) ['foo' => 'bar']]) extends AbstractData {
            public stdClass $foo;
        };

        self::assertEquals('{"foo":{"foo":"bar"}}', $data->toJson());
    }
}
