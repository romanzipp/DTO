<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class SimpleDataTypeHintedRequired extends AbstractData
{
    #[Required]
    public string $foo;
}
