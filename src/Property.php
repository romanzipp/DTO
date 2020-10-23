<?php

declare(strict_types=1);

namespace romanzipp\DTO;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Values\MissingValue;

final class Property
{
    private ReflectionProperty $reflectionProperty;

    private ?AbstractData $data;

    public string $name;

    public bool $hasType;

    public bool $isRequired;

    public bool $isInitialized;

    public bool $allowsNull;

    public bool $hasDefaultValue;

    /**
     * @var \romanzipp\DTO\Types\Type[]
     */
    public array $allowedTypes;

    public function __construct(ReflectionProperty $reflectionProperty, ?AbstractData $data = null)
    {
        $this->reflectionProperty = $reflectionProperty;
        $this->data = $data;

        $this->name = $this->checkGetName();

        $this->hasType = $this->checkHasType();
        $this->allowsNull = $this->checkAllowsNull();
        $this->isRequired = $this->checkIsRequired();

        if (null !== $data) {
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
     *
     * @return static
     */
    public static function make(ReflectionProperty $reflectionProperty, AbstractData $data): self
    {
        return new self($reflectionProperty, $data);
    }

    /**
     * Collect all properties form a given class and optional instance.
     *
     * @param string $class
     * @param \romanzipp\DTO\AbstractData|null $data
     *
     * @return \romanzipp\DTO\Property[]
     */
    public static function collect(string $class, ?AbstractData $data = null): array
    {
        $properties = [];

        try {
            $reflectionClass = new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            return $properties;
        }

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $properties[$property->getName()] = new self($property, $data);
        }

        return $properties;
    }

    /**
     * @param \romanzipp\DTO\AbstractData $data
     *
     * @return \romanzipp\DTO\Property[]
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
     *
     * @return \romanzipp\DTO\Property[]
     */
    public static function collectFromClass(string $class): array
    {
        return self::collect($class);
    }

    /**
     * Check if a given value is valid for the current property.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        return null === $this->getError($value);
    }

    /**
     * Get the validation error for a given value.
     *
     * @param mixed $value
     *
     * @return \romanzipp\DTO\Exceptions\InvalidDataException|null
     */
    public function getError($value): ?InvalidDataException
    {
        if ($this->isRequired && $value instanceof MissingValue) {
            return InvalidDataException::requiredPropertyMissing($this);
        }

        if (null === $value) {
            if ( ! $this->allowsNull) {
                return InvalidDataException::nullNotAllowed($this);
            }

            return null;
        }

        if ( ! $this->hasType) {
            return null;
        }

        if ($value instanceof MissingValue) {
            return null;
        }

        foreach ($this->allowedTypes as $type) {
            if ( ! $type->isValid($value)) {
                continue;
            }

            return null;
        }

        return InvalidDataException::invalidType($this, $value);
    }

    /**
     * @param array $data
     *
     * @return \romanzipp\DTO\Values\MissingValue|mixed
     */
    public function extractValueFromData(array $data)
    {
        if ( ! array_key_exists($this->name, $data)) {
            return new MissingValue();
        }

        return $data[$this->name];
    }

    /**
     * Check if the property has been correctly set up.
     *
     * @return bool
     */
    public function isCorrectlyDeclared(): bool
    {
        return ! ($this->hasType && $this->allowsNull && $this->isInitialized && $this->isRequired);
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
        return true === $this->reflectionProperty->hasType();
    }

    /**
     * Check if the property has been included in the `$required` data property.
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
     *
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
     * NOTE: This method also returns true of the property is nullable `?`.
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

        // TODO: Implement \ReflectionUnionType for PHP 8.0+

        if ($type instanceof ReflectionNamedType) {
            return [
                new Types\NamedReflectedType($type),
            ];
        }

        if ($type instanceof ReflectionType && ! $type->allowsNull()) {
            return [
                new Types\NotNullType(),
            ];
        }

        return [];
    }
}
