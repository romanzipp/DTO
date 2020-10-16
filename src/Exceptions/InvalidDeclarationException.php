<?php

namespace romanzipp\DTO\Exceptions;

use Exception;
use romanzipp\DTO\Property;

class InvalidDeclarationException extends Exception
{
    public static function fromProperty(Property $property): self
    {
        return new self("The property `{$property->name}` has been declared as nullable with default value but marked as required");
    }
}
