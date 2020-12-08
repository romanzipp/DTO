<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class SimpleDataTypeUnionNullableRequired extends AbstractData
{
    #[Required]
    public string|int|null $foo;
}
