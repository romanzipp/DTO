<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;

class SimpleDataNullableDefaultNull extends AbstractData
{
    public ?string $foo = null;
}
