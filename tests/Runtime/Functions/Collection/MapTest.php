<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use ArrayIterator;
use Generator;
use PHPUnit\Framework\TestCase;

use function Fp\Collection\map;

final class MapTest extends TestCase
{
    /**
     * @param iterable<array-key, int> $source
     *
     * @dataProvider provideCases
     */
    public function testMap(iterable $source, array $expected): void
    {
        $this->assertEquals(
            $expected,
            map(
                $source,
                fn(int $v) => (string) ($v + 1)
            )
        );
    }

    /**
     * @psalm-type ShapeToYield = array{source: iterable<array-key, int>, shouldBe: array<array-key, string>}
     *
     * @return Generator<string, ShapeToYield, mixed, void>
     */
    public function provideCases(): Generator
    {
        yield 'array' => [
            'source' => ['a' => 1, 'b' => 2, 'c' => 3],
            'shouldBe' => ['a' => '2', 'b' => '3', 'c' => '4'],
        ];

        yield 'list' => [
            'source' => [1, 2, 3],
            'shouldBe' => ['2', '3', '4']
        ];

        yield 'ArrayIterator' => [
            'source' => new  ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3]),
            'shouldBe' => ['a' => '2', 'b' => '3', 'c' => '4']
        ];

        yield 'ArrayIterator from list' => [
            'source' => new  ArrayIterator([1, 2, 3]),
            'shouldBe' => ['2', '3', '4']
        ];

        yield 'Generator' => [
            'source' => (fn() => yield from ['a' => 1, 'b' => 2, 'c' => 3])(),
            'shouldBe' => ['a' => '2', 'b' => '3', 'c' => '4']
        ];

        yield 'Generator from list' => [
            'source' => (fn() => yield from [1, 2, 3])(),
            'shouldBe' => ['2', '3', '4']
        ];
    }
}
