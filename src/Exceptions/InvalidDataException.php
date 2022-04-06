<?php

declare(strict_types=1);

namespace romanzipp\DTO\Exceptions;

use InvalidArgumentException;
use romanzipp\DTO\Property;

class InvalidDataException extends InvalidArgumentException
{
    /**
     * @var \romanzipp\DTO\Property[]
     */
    private array $properties = [];

    /**
     * @param \romanzipp\DTO\Property $property
     * @param mixed $value
     *
     * @return self
     */
    public static function invalidType(Property $property, $value): self
    {
        $type = gettype($value);

        if (is_object($value)) {
            $type = get_class($value);
        }

        $exception = new self("The type `{$type}` is not allowed for property `{$property->name}`");
        $exception->setProperties([$property]);

        return $exception;
    }

    public static function requiredPropertyMissing(Property $property): self
    {
        $exception = new self("The required property `{$property->name}` is missing");
        $exception->setProperties([$property]);

        return $exception;
    }

    public static function nullNotAllowed(Property $property): self
    {
        $exception = new self("`NULL` is not allowed for property `{$property->name}`");
        $exception->setProperties([$property]);

        return $exception;
    }

    /**
     * @param string[] $keys
     *
     * @return self
     */
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
     *
     * @return self
     */
    public static function any(array $exceptions): self
    {
        if (1 === count($exceptions)) {
            return array_shift($exceptions);
        }

        $messages = [];
        $properties = [];

        foreach ($exceptions as $exception) {
            $messages[] = $exception->getMessage();
            $properties = array_merge($properties, $exception->getProperties());
        }

        $exception = new self(
            implode(PHP_EOL, $messages)
        );
        $exception->setProperties($properties);

        return $exception;
    }

    /**
     * @param \romanzipp\DTO\Property[] $properties
     *
     * @return void
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return \romanzipp\DTO\Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
