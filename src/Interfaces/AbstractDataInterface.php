<?php

namespace romanzipp\DTO\Interfaces;

interface AbstractDataInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = []);
}
