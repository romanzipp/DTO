<?php

declare(strict_types=1);

namespace romanzipp\DTO\Cases;

abstract class AbstractCase
{
    protected array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Convert all keys and return the array.
     *
     * @return array
     */
    public function format(): array
    {
        return array_combine(
            $this->formatKeys(
                array_keys($this->values)
            ),
            array_values($this->values)
        );
    }

    /**
     * Convert string to pascal case.
     *
     * @param string $key
     *
     * @return string
     */
    protected static function toPascalCase(string $key): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
    }

    /**
     * Join a given delimiter on case breaks.
     *
     * @param string $key
     * @param string $delimiter
     *
     * @return string
     */
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
