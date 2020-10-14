<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;

class SimpleDataRequired extends AbstractData
{
    protected static array $required = [
        'foo',
    ];

    public $foo;
}
