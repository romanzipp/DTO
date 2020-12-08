<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;

class SimpleDataTypeUnion extends AbstractData
{
    public string|int $foo;
}
