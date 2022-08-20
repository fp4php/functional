<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\Mock\Foo;

use function Fp\Collection\pluck;

final class PluckStaticTest
{
    /**
     * @param list<array{name: string, age: int}> $list
     * @return list<string>
     */
    public function testListShape(array $list): array
    {
        return pluck($list, 'name');
    }

    /**
     * @param non-empty-list<array{name: string, age: int}> $list
     * @return non-empty-list<string>
     */
    public function testNonEmptyListShape(array $list): array
    {
        return pluck($list, 'name');
    }

    /**
     * @param array<int, array{name: string, age: int}> $list
     * @return array<int, string>
     */
    public function testArrayShape(array $list): array
    {
        return pluck($list, 'name');
    }

    /**
     * @param non-empty-array<int, array{name: string, age: int}> $array
     * @return non-empty-array<int, string>
     */
    public function testNonEmptyArrayShape(array $array): array
    {
        return pluck($array, 'name');
    }

    /**
     * @param list<Foo> $list
     * @return list<int>
     */
    public function testObjectList(array $list): array
    {
        return pluck($list, 'a');
    }

    /**
     * @param list<Foo> $list
     */
    public function testObjectListUndefinedPropertyFetch(array $list): array
    {
        /** @psalm-suppress UndefinedPropertyFetch */
        return pluck($list, 'undefined');
    }

    /**
     * @param list<array{name: string, age: int}> $list
     */
    public function testShapeListUndefinedArrayKey(array $list): array
    {
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        return pluck($list, 'undefined');
    }
}
