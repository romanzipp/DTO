<?php

namespace romanzipp\DTO\Tests;

use romanzipp\DTO\Cases\CamelCase;
use romanzipp\DTO\Cases\KebabCase;
use romanzipp\DTO\Cases\PascalCase;
use romanzipp\DTO\Cases\SnakeCase;
use romanzipp\DTO\Tests\Support\NestedData;

class NestedDataValuesTest extends TestCase
{
    public function testBasic()
    {
        $data = new NestedData([
            'childData' => new NestedData([
                'childData' => new NestedData([]),
            ]),
        ]);

        self::assertEquals(['childData' => ['childData' => []]], $data->toArray());
        self::assertEquals(['childData' => ['childData' => []]], $data->toArrayConverted(CamelCase::class));
        self::assertEquals(['child-data' => ['child-data' => []]], $data->toArrayConverted(KebabCase::class));
        self::assertEquals(['ChildData' => ['ChildData' => []]], $data->toArrayConverted(PascalCase::class));
        self::assertEquals(['child_data' => ['child_data' => []]], $data->toArrayConverted(SnakeCase::class));
    }
}
