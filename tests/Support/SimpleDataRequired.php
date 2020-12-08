<?php

namespace romanzipp\DTO\Tests\Support;

use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class SimpleDataRequired extends AbstractData
{
    #[Required]
    public $foo;
}
