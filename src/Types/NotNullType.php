<?php

declare(strict_types=1);

namespace romanzipp\DTO\Types;

class NotNullType implements Type
{
    public function isValid($value): bool
    {
        return null !== $value;
    }
}
