<?php

namespace romanzipp\DTO\Types;

interface Type
{
    /**
     * Check if the given value is valid.
     *
     * @param $value
     *
     * @return bool
     */
    public function isValid($value): bool;
}
