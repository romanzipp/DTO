<?php

declare(strict_types=1);

namespace romanzipp\DTO;

use romanzipp\DTO\Attributes\Flexible;
use romanzipp\DTO\Cases\AbstractCase;
use romanzipp\DTO\Cases\SnakeCase;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Interfaces\AbstractDataInterface;
use romanzipp\DTO\Values\MissingValue;

abstract class AbstractData implements AbstractDataInterface, \JsonSerializable
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        // Analyse the declared properties
        $properties = Property::collectFromInstance($this);

        // Collect errors instead of throwing the first exception to make working with
        // large sets of properties less of a hassle
        $errors = [];

        foreach ($properties as $property) {
            if ( ! $property->isCorrectlyDeclared()) {
                throw InvalidDeclarationException::fromProperty($property);
            }

            // Get the property value from provided data
            $value = $property->extractValueFromData($data);

            if ( ! $property->isValid($value)) {
                $errors[] = $property->getError($value);

                continue;
            }

            // Do not set missing values
            if ($value instanceof MissingValue) {
                continue;
            }

            $this->{$property->getName()} = $value;
        }

        if ( ! empty($errors)) {
            throw InvalidDataException::any($errors);
        }

        // Calculate keys that are provided but not declared as properties
        $diff = array_diff_key($data, $properties);

        // Fail if there are additional properties but the instance is not flexible
        if (false === static::isFlexible() && count($diff) > 0) {
            throw InvalidDataException::notFlexible(array_keys($diff));
        }

        // Set additional properties
        foreach ($diff as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Create an instance from given data array.
     *
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data = []): static
    {
        return new static($data);
    }

    /**
     * Determine if the dto is flexible and will accept more properties than declared.
     *
     * @return bool
     */
    public static function isFlexible(): bool
    {
        return ! empty(
            (new \ReflectionClass(static::class))->getAttributes(Flexible::class)
        );
    }

    /**
     * Get the property instance for a given key.
     *
     * @param string $key
     *
     * @return \romanzipp\DTO\Property
     */
    private function getProperty(string $key): Property
    {
        return Property::fromKey($key, $this);
    }

    /**
     * Determine if a property has been initialized with a value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isset(string $key): bool
    {
        return array_key_exists($this->getProperty($key)->getName(), get_object_vars($this));
    }

    /**
     * Get public values.
     *
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return get_object_vars($this);
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get a string of json formatted values.
     *
     * @throws \JsonException
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }

    /**
     * Get an array of properties (includes flexible).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->walkValuesDataCallback(fn (self $value) => $value->toArray());
    }

    /**
     * Get an array of properties with converted keys (includes flexible).
     *
     * @param string $case
     *
     * @return array<string, mixed>
     */
    public function toArrayConverted(string $case = SnakeCase::class): array
    {
        $values = $this->walkValuesDataCallback(fn (self $value) => $value->toArrayConverted($case));

        if ( ! is_subclass_of($case, AbstractCase::class)) {
            throw new \InvalidArgumentException("The given case formatter `{$case}` is invalid");
        }

        /** @var \romanzipp\DTO\Cases\AbstractCase $caseFormatter */
        $caseFormatter = new $case($values);

        return $caseFormatter->format();
    }

    /**
     * Iterate over instance values with a given callback applied to DTO instances.
     *
     * @param \Closure $callback
     *
     * @return array<string, mixed>
     */
    private function walkValuesDataCallback(\Closure $callback): array
    {
        $serializeItem = static function ($value, \Closure $callback) {
            if ($value instanceof self) {
                return $callback($value);
            }

            if ($value instanceof \JsonSerializable) {
                return $value->jsonSerialize();
            }

            return $value;
        };

        return array_map(static function ($value) use ($callback, $serializeItem) {
            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    $value[$key] = $serializeItem($item, $callback);
                }
            }

            return $serializeItem($value, $callback);
        }, $this->getValues());
    }
}
