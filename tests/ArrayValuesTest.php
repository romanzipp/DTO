<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\AbstractData;

class ArrayValuesTest extends TestCase
{
    public function testBasicArray()
    {
        $data = new class(['array' => []]) extends AbstractData {
            public array $array;
        };

        self::assertSame([], $data->array);
    }
}
