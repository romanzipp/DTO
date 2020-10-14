<?php

namespace romanzipp\DTO;

use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Values\MissingValue;

abstract class AbstractData
{
    protected static array $required = [];

    public function __construct(array $data = [])
    {
        $attributes = Attribute::collectFromInstance($this);

        $errors = [];

        foreach ($attributes as $attribute) {

            if ( ! $attribute->isCorrectlyDeclared()) {
                throw InvalidDeclarationException::fromAttribute($attribute);
            }

            $value = new MissingValue;

            if (array_key_exists($attribute->name, $data)) {
                $value = $data[$attribute->name];
            }

            if ( ! $attribute->isValid($value)) {

                $errors[] = $attribute->getError($value);

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
