<?php

namespace romanzipp\DTO\Cases;

class PascalCase extends AbstractCase
{
    protected function formatKeys(array $keys): array
    {
        return array_map(static fn(string $key) => self::toPascalCase($key), $keys);
    }
}
