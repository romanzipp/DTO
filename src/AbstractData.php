<?php

namespace romanzipp\DTO;

use InvalidArgumentException;
use JsonSerializable;
use romanzipp\DTO\Exceptions\InvalidDataException;
use romanzipp\DTO\Exceptions\InvalidDeclarationException;
use romanzipp\DTO\Values\MissingValue;

abstract class AbstractData implements JsonSerializable
{
    private const RESERVED_ATTRIBUTES = [
        'required',
        'flexible',
        'attributes',
    ];

    /**
     * Define attributes which must be specified when creating a new data instance.
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
     * The parsed attributes.
     *
     * @var \romanzipp\DTO\Attribute[]
     */
    protected array $attributes = [];

    public function __construct(array $data = [])
    {
        // Analyse the declared attributes
        $this->attributes = Attribute::collectFromInstance($this);

        // Collect errors instead of throwing the first exception to make working with
        // large sets of attribute less of a hassle
        $errors = [];

        foreach ($this->attributes as $attribute) {

            if ( ! $attribute->isCorrectlyDeclared()) {
                throw InvalidDeclarationException::fromAttribute($attribute);
            }

            // Get the attribute value from provided data
            $value = $attribute->extractValueFromData($data);

            if ( ! $attribute->isValid($value)) {

                $errors[] = $attribute->getError($value);

                continue;
            }

            // Do not set missing values
            if ($value instanceof MissingValue) {
                continue;
            }

            $this->{$attribute->name} = $value;
        }

        if ( ! empty($errors)) {
            throw InvalidDataException::any($errors);
        }

        // Calculate keys that are provided but not declared as attributes
        $diff = array_diff_key($data, $this->attributes);

        // Fail if there are additional attributes but the instance is not flexible
        if (static::$flexible === false && count($diff) > 0) {
            throw InvalidDataException::notFlexible(
                array_keys($diff)
            );
        }

        // Set additional attributes
        foreach ($diff as $key => $value) {
            $this->{$key} = $value;
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
     * Get an array of attributes (includes flexible).
     *
     * @param string|null $case
     * @return array
     */
    public function toArray(?string $case = null): array
    {
        // $values = array_map(fn(Attribute $attribute) => $this->{$attribute->name}, Attribute::collectFromInstance($this));

        $values = array_filter(
            get_object_vars($this),
            static fn(string $key) => ! in_array($key, self::RESERVED_ATTRIBUTES, true),
            ARRAY_FILTER_USE_KEY
        );

        if ($case === null) {
            return $values;
        }

        /** @var \romanzipp\DTO\Strings\AbstractCase $caseFormatter */
        $caseFormatter = new $case($values);

        return $caseFormatter->format();
    }
}
