<?php

declare(strict_types=1);

namespace romanzipp\DTO\Types;

use ReflectionNamedType;
use ReflectionUnionType;

class UnionType implements Type
{
    private ReflectionUnionType $type;

    public function __construct(ReflectionUnionType $type)
    {
        $this->type = $type;
    }

    public function isValid(mixed $value): bool
    {
        $types = array_map(static fn (ReflectionNamedType $type) => new NamedReflectedType($type), $this->type->getTypes());

        foreach ($types as $type) {
            if ($type->isValid($value)) {
                return true;
            }
        }

        return false;
    }
}
