<?php

namespace romanzipp\DTO\Strings;

class SnakeCase extends AbstractCase
{
    protected function formatKeys(array $keys): array
    {
        return array_map(static fn(string $key) => self::joinDelimiter($key, '_'), $keys);
    }
}
