<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;

class SimpleDataTypeHintedRequired extends AbstractData
{
    protected static array $required = [
        'foo',
    ];

    public string $foo;
}
