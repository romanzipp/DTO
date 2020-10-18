<?php

namespace romanzipp\DTO;

use InvalidArgumentException;
use JsonSerializable;
use romanzipp\DTO\Cases\SnakeCase;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Values\MissingValue;

abstract class AbstractData implements JsonSerializable
{
    private const RESERVED_PROPERTIES = [
        'required',
        'flexible',
        'properties',
    ];

    /**
     * Define properties which must be specified when creating a new data instance.
     *
     * @var array
     */
    protected static array $required = [];

    /**
     * Define weather you can pass other values other than defined.
     *
     * @var bool
     */
    protected static bool $flexible = false;

    /**
     * The parsed properties.
     *
     * @var \romanzipp\DTO\Property[]
     */
    protected array $properties = [];

    public function __construct(array $data = [])
    {
        // Analyse the declared properties
        $this->properties = Property::collectFromInstance($this);

        // Collect errors instead of throwing the first exception to make working with
        // large sets of properties less of a hassle
        $errors = [];

        foreach ($this->properties as $property) {
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

            $this->{$property->name} = $value;
        }

        if ( ! empty($errors)) {
            throw InvalidDataException::any($errors);
        }

        // Calculate keys that are provided but not declared as properties
        $diff = array_diff_key($data, $this->properties);

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
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data = [])
    {
        return new static($data);
    }

    /**
     * Get the array of required properties.
     *
     * @return array
     */
    public static function getRequired(): array
    {
        return static::$required;
    }

    /**
     * Determine if the dto is flexible and will accept more properties than declared.
     *
     * @return bool
     */
    public static function isFlexible(): bool
    {
        return static::$flexible;
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
        if ( ! array_key_exists($key, $this->properties)) {
            throw new InvalidArgumentException("Can not access missing data property `{$key}`");
        }

        return $this->properties[$key];
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
        return array_key_exists($this->getProperty($key)->name, get_object_vars($this));
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get an array of properties (includes flexible).
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter(
            get_object_vars($this),
            static fn (string $key) => ! in_array($key, self::RESERVED_PROPERTIES, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Get an array of properties with converted keys (includes flexible).
     *
     * @param string $case
     *
     * @return array
     */
    public function toArrayConverted(string $case = SnakeCase::class): array
    {
        $values = $this->toArray();

        /** @var \romanzipp\DTO\Cases\AbstractCase $caseFormatter */
        $caseFormatter = new $case($values);

        return $caseFormatter->format();
    }
}
