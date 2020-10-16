<?php

namespace romanzipp\DTO\Exceptions;

use romanzipp\DTO\Property;
use RuntimeException;

class InvalidDeclarationException extends RuntimeException
{
    public static function fromProperty(Property $property): self
    {
        return new self("The property `{$property->name}` has been declared as nullable with default value but marked as required");
    }
}
