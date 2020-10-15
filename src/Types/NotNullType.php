<?php

namespace romanzipp\DTO\Types;

class NotNullType implements Type
{
    public function isValid($value): bool
    {
        return $value !== null;
    }
}
