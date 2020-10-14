<?php

namespace romanzipp\DTO;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Values\MissingValue;

final class Attribute
{
    private ReflectionProperty $reflectionProperty;

    private ?AbstractData $data;

    public string $name;

    public bool $hasType;

    public bool $isRequired;

    public bool $isInitialized;

    public bool $allowsNull;

    public bool $hasDefaultValue;

    public array $allowedTypes;

    public function __construct(ReflectionProperty $reflectionProperty, ?AbstractData $data = null)
    {
        $this->reflectionProperty = $reflectionProperty;
        $this->data = $data;

        $this->name = $this->checkGetName();

        $this->hasType = $this->checkHasType();
        $this->allowsNull = $this->checkAllowsNull();
        $this->isRequired = $this->checkIsRequired();

        if ($data !== null) {
            $this->isInitialized = $this->checkIsInitialized($data);
        }

        $this->hasDefaultValue = $this->checkHasDefaultValue();
        $this->allowedTypes = $this->checkAllowedTypes();
    }

    /**
     * Create a new class instance.
     *
     * @param \ReflectionProperty $reflectionProperty
     * @param \romanzipp\DTO\AbstractData $data
     * @return static
     */
    public static function make(ReflectionProperty $reflectionProperty, AbstractData $data): self
    {
        return new self($reflectionProperty, $data);
    }

    /**
     * Collect all attributes form a given class and optional instance.
     *
     * @param string $class
     * @param \romanzipp\DTO\AbstractData|null $data
     * @return \romanzipp\DTO\Attribute[]
     */
    public static function collect(string $class, ?AbstractData $data = null): array
    {
        $reflectionClass = new ReflectionClass($class);

        $attributes = [];

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {

            if ($property->isStatic()) {
                continue;
            }

            $attributes[$property->getName()] = new self($property, $data);
        }

        return $attributes;
    }

    /**
     * @param \romanzipp\DTO\AbstractData $data
     * @return \romanzipp\DTO\Attribute[]
     */
    public static function collectFromInstance(AbstractData $data): array
    {
        return self::collect(
            get_class($data),
            $data
        );
    }

    /**
     * @param string $class
     * @return \romanzipp\DTO\Attribute[]
     */
    public static function collectFromClass(string $class): array
    {
        return self::collect($class);
    }

    /**
     * Check if a given value is valid for the current attribute.
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        return $this->getError($value) === null;
    }

    /**
     * Get the validation error for a given value.
     *
     * @param mixed $value
     * @return \romanzipp\DTO\Exceptions\InvalidDataException|null
     */
    public function getError($value): ?InvalidDataException
    {
        if ($this->isRequired && $value instanceof MissingValue) {
            return InvalidDataException::requiredAttributeMissing($this);
        }

        if ($value === null) {

            if ( ! $this->allowsNull) {
                return InvalidDataException::nullNotAllowed($this);
            }

            return null;
        }

        if ( ! $this->hasType) {
            return null;
        }

        foreach ($this->allowedTypes as $allowedType) {

            if ($value instanceof $allowedType) {
                return null;
            }

            if (gettype($value) === $allowedType) {
                return null;
            }
        }

        return InvalidDataException::invalidType($this, $value);
    }

    /**
     * Check if the property has been correctly set up.
     *
     * @return bool
     */
    public function isCorrectlyDeclared(): bool
    {
        if ($this->hasType && $this->allowsNull && $this->isInitialized && $this->isRequired) {
            return false;
        }

        return true;
    }

    public function mustBeInitialized(): bool
    {
        return $this->isRequired || ($this->allowsNull);
    }

    /*
     *--------------------------------------------------------------------------
     * Reflection property validations called once
     *--------------------------------------------------------------------------
     */

    /**
     * Get the property name.
     *
     * @return string
     */
    private function checkGetName(): string
    {
        return $this->reflectionProperty->getName();
    }

    /**
     * Check if a type has been defined.
     *
     * @return bool
     */
    private function checkHasType(): bool
    {
        return $this->reflectionProperty->hasType() === true;
    }

    /**
     * Check if the property has been included in the `$required` data attribute.
     *
     * @return bool
     */
    private function checkIsRequired(): bool
    {
        return in_array(
            $this->reflectionProperty->getName(),
            $this->data ? $this->data::getRequired() : $this->reflectionProperty->class::getRequired(),
            true
        );
    }

    /**
     * Check if a property has been initialized with a value.
     * This also returns true if a property has been declared with a default value.
     *
     * @param \romanzipp\DTO\AbstractData $data
     * @return bool
     */
    private function checkIsInitialized(AbstractData $data): bool
    {
        return $this->reflectionProperty->isInitialized($data);
    }

    /**
     * Check if the property allows setting null.
     *
     * @return bool
     */
    private function checkAllowsNull(): bool
    {
        if ( ! $type = $this->reflectionProperty->getType()) {
            return true;
        }

        return $type->allowsNull();
    }

    /**
     * NOTE: This method also returns true of the property is nullable `?`
     *
     * @return bool
     */
    private function checkHasDefaultValue(): bool
    {
        return $this->reflectionProperty->isDefault();
    }

    /**
     * Get all allowed types.
     *
     * @return array
     */
    private function checkAllowedTypes(): array
    {
        if ( ! $type = $this->reflectionProperty->getType()) {
            return [];
        }

        if ($type instanceof ReflectionNamedType) {
            return [
                $type->getName(),
            ];
        }

        return [];
    }
}
