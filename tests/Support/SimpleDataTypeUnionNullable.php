<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;

class SimpleDataTypeUnionNullable extends AbstractData
{
    public string|int|null $foo;
}
