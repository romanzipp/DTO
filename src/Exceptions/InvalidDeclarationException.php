<?php

namespace romanzipp\DTO\Exceptions;

use Exception;
use romanzipp\DTO\Attribute;

class InvalidDeclarationException extends Exception
{
    public static function fromAttribute(Attribute $attribute): self
    {
        return new self("The attribute `{$attribute->name}` has been declared as nullable with default value but marked as required");
    }
}
