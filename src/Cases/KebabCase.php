<?php

declare(strict_types=1);

namespace romanzipp\DTO\Cases;

class KebabCase extends AbstractCase
{
    protected function formatKeys(array $keys): array
    {
        return array_map(static fn (string $key) => self::joinDelimiter(str_replace('_', '-', $key), '-'), $keys);
    }
}
