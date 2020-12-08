<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class SimpleDataNullableRequired extends AbstractData
{
    #[Required]
    public ?string $foo;
}
