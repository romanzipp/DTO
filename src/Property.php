<?php

declare(strict_types=1);

namespace romanzipp\DTO;

use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Values\MissingValue;

final class Property
{
    private \ReflectionProperty $reflectionProperty;

    private ?AbstractData $data;

    private string $name;

    private bool $hasType;

    private bool $isRequired;

    private bool $isInitialized;

    private bool $allowsNull;

    private bool $hasDefaultValue;

    /**
     * @var \romanzipp\DTO\Types\Type[]
     */
    public array $allowedTypes;

    public function __construct(\ReflectionProperty $reflectionProperty, ?AbstractData $data = null)
    {
        $this->reflectionProperty = $reflectionProperty;
        $this->data = $data;

        $this->name = $this->checkGetName();

        $this->hasType = $this->checkHasType();
        $this->allowsNull = $this->checkAllowsNull();
        $this->isRequired = $this->checkIsRequired();

        if (null !== $this->data) {
            $this->isInitialized = $this->checkIsInitialized($this->data);
        }

        $this->hasDefaultValue = $this->checkHasDefaultValue();
        $this->allowedTypes = $this->checkAllowedTypes();
    }

    /**
     * Create a new class instance.
     *
     * @param \ReflectionProperty $reflectionProperty
     * @param AbstractData $data
     *
     * @return static
     */
    public static function make(\ReflectionProperty $reflectionProperty, AbstractData $data): self
    {
        return new self($reflectionProperty, $data);
    }

    /**
     * Collect all properties form a given class and optional instance.
     *
     * @param string $class
     * @param AbstractData|null $data
     *
     * @return \romanzipp\DTO\Property[]
     */
    public static function collect(string $class, ?AbstractData $data = null): array
    {
        $properties = [];

        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return $properties;
        }

        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $properties[$property->getName()] = new self($property, $data);
        }

        return $properties;
    }

    /**
     * Create an instance from a given data instance and property key.
     *
     * @param string $key
     * @param AbstractData $data
     *
     * @return $this
     */
    public static function fromKey(string $key, AbstractData $data): self
    {
        return new self(new \ReflectionProperty($data, $key), $data);
    }

    /**
     * @param AbstractData $data
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
    public function isValid(mixed $value): bool
    {
        return null === $this->getError($value);
    }

    /**
     * Get the validation error for a given value.
     *
     * @param mixed $value
     *
     * @return InvalidDataException|null
     */
    public function getError(mixed $value): ?InvalidDataException
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
     * @param array<string, mixed> $data
     *
     * @return mixed
     */
    public function extractValueFromData(array $data): mixed
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

    public function getName(): string
    {
        return $this->name;
    }

    public function hasType(): bool
    {
        return $this->hasType;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function isInitialized(): bool
    {
        return $this->isInitialized;
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
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
        return ! empty($this->reflectionProperty->getAttributes(Attributes\Required::class));
    }

    /**
     * Check if a property has been initialized with a value.
     * This also returns true if a property has been declared with a default value.
     *
     * @param AbstractData $data
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
     * @return \romanzipp\DTO\Types\Type[]
     */
    private function checkAllowedTypes(): array
    {
        if ( ! $type = $this->reflectionProperty->getType()) {
            return [];
        }

        if ($type instanceof \ReflectionUnionType) {
            return [
                new Types\UnionType($type),
            ];
        }

        if ($type instanceof \ReflectionNamedType) {
            return [
                new Types\NamedReflectedType($type),
            ];
        }

        if ($type instanceof \ReflectionType && ! $type->allowsNull()) {
            return [
                new Types\NotNullType(),
            ];
        }

        return [];
    }
}
