<?php

namespace romanzipp\DTO;

use InvalidArgumentException;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Values\MissingValue;

abstract class AbstractData
{
    protected static array $required = [];

    protected array $attributes = [];

    public function __construct(array $data = [])
    {
        $this->attributes = Attribute::collectFromInstance($this);

        $errors = [];

        foreach ($this->attributes as $attribute) {

            if ( ! $attribute->isCorrectlyDeclared()) {
                throw InvalidDeclarationException::fromAttribute($attribute);
            }

            $value = $attribute->extractValueFromData($data);

            if ( ! $attribute->isValid($value)) {

                $errors[] = $attribute->getError($value);

                continue;
            }

            if ($value instanceof MissingValue) {
                continue;
            }

            $this->{$attribute->name} = $value;
        }

        if ( ! empty($errors)) {
            throw InvalidDataException::any($errors);
        }
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data = [])
    {
        return new static($data);
    }

    /**
     * Get the array of required attributes.
     *
     * @return array
     */
    public static function getRequired(): array
    {
        return static::$required;
    }

    /**
     * Get the attribute instance for a given attribute key.
     *
     * @param string $key
     * @return \romanzipp\DTO\Attribute
     */
    private function getAttribute(string $key): Attribute
    {
        if ( ! array_key_exists($key, $this->attributes)) {
            throw new InvalidArgumentException("Can not access missing data attribute `{$key}`");
        }

        return $this->attributes[$key];
    }

    /**
     * Determine if the attribute has been initialized with a value.
     *
     * @param string $key
     * @return bool
     */
    public function isset(string $key): bool
    {
        return array_key_exists($this->getAttribute($key)->name, get_object_vars($this));
    }
}
