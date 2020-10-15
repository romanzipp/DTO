<?php

namespace romanzipp\DTO\Strings;

class PascalCase extends AbstractCase
{
    protected function formatKeys(array $keys): array
    {
        return array_map(static fn(string $key) => self::toPascalCase($key), $keys);
    }
}
