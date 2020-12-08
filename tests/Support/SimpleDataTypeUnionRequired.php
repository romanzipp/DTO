<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class SimpleDataTypeUnionRequired extends AbstractData
{
    #[Required]
    public string|int $foo;
}
