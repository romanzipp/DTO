<?php

namespace romanzipp\DTO\Strings;

abstract class AbstractCase
{
    protected array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function format(): array
    {
        return array_combine(
            $this->formatKeys(
                array_keys($this->values)
            ),
            array_values($this->values)
        );
    }

    protected static function toPascalCase(string $key): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
    }

    protected static function joinDelimiter(string $key, string $delimiter): string
    {
        if ( ! ctype_lower($key)) {
            $key = preg_replace('/\s+/u', '', ucwords($key));
            $key = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $key), 'UTF-8');
        }

        return $key;
    }

    abstract protected function formatKeys(array $keys): array;
}
