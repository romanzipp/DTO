<?php

declare(strict_types=1);

namespace romanzipp\DTO\Types;

class NamedReflectedType implements Type
{
    /**
     * Maps type names from \ReflectionNamedType to values matching gettype() output.
     *
     * @see \ReflectionNamedType
     */
    private const TYPES_MAPPING = [
        'bool' => 'boolean',
        'float' => 'double',
        'int' => 'integer',
    ];

    private \ReflectionNamedType $type;

    public function __construct(\ReflectionNamedType $type)
    {
        $this->type = $type;
    }

    public function isValid(mixed $value): bool
    {
        $name = $this->type->getName();

        if ( ! $this->type->isBuiltin()) {
            return $value instanceof $name;
        }

        if (isset(self::TYPES_MAPPING[$name])) {
            $name = self::TYPES_MAPPING[$name];
        }

        return gettype($value) === $name;
    }
}
