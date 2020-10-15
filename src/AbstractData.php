<?php

namespace romanzipp\DTO;

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

    public static function getRequired(): array
    {
        return static::$required;
    }
}
