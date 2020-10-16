<?php

namespace romanzipp\DTO\Exceptions;

use Exception;
use romanzipp\DTO\Property;

class InvalidDataException extends Exception
{
    public static function fromProperty(Property $property): self
    {
        return new self("Invalid property");
    }

    public static function invalidType(Property $property, $value): self
    {
        $type = gettype($value);

        if (is_object($value)) {
            $type = get_class($value);
        }

        return new self("The type `{$type}` is not allowed for property `{$property->name}`");
    }

    public static function requiredPropertyMissing(Property $property): self
    {
        return new self("The required property `{$property->name}` is missing");
    }

    public static function nullNotAllowed(Property $property): self
    {
        return new self("`NULL` is not allowed for property `{$property->name}`");
    }

    public static function notFlexible(array $keys): self
    {
        if (count($keys) > 0) {
            return new self(
                sprintf('The provided values `%s` are not declared as properties', implode('`, `', $keys))
            );
        }

        return new self('Some provided values are not declared as properties');
    }

    /**
     * @param \romanzipp\DTO\Exceptions\InvalidDataException[] $exceptions
     * @return static
     */
    public static function any(array $exceptions): self
    {
        if (count($exceptions) === 1) {
            return array_shift($exceptions);
        }

        $messages = [];

        foreach ($exceptions as $exception) {
            $messages[] = $exception->getMessage();
        }

        return new self(
            implode(PHP_EOL, $messages)
        );
    }
}
