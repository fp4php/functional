<?php

declare(strict_types=1);

namespace Tests\Static\Collection;

use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\PhpBlockTestCase;

final class PartitionTest extends PhpBlockTestCase
{
    public function testPartitionWithOnePredicate(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\partition;$result = partition(
                [2, 3, 4, 5], 
                fn(int $v) => $v % 2 === 0
            );
        ';

        $this->assertBlockType($phpBlock, 'array{0: array<0|1|2|3, 2|3|4|5>, 1: array<0|1|2|3, 2|3|4|5>}');
    }

    public function testPartitionWithTwoPredicates(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\partition;$result = partition(
                [1],
                fn(int $v) => $v % 2 === 0,
                fn(int $v) => $v % 2 === 1,
            );
        ';

        $this->assertBlockType($phpBlock, 'array{0: array<0, 1>, 1: array<0, 1>, 2: array<0, 1>}');
    }

    public function testPartitionOfWithOneClass(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use Tests\Mock\Bar;
            use Tests\Mock\Foo;
            use function Fp\Collection\partitionOf;
            
            $result = partitionOf(
                [new Foo(1), new Bar(true)],
                false,
                Foo::class
            );
        ';

        $this->assertBlockType($phpBlock, strtr(
            'array{list<Foo>, list<Bar|Foo>}',
            [
                'Foo' => Foo::class,
                'Bar' => Bar::class,
            ]
        ));
    }

    public function testPartitionOfWithThreeClasses(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use Tests\Mock\Bar;
            use Tests\Mock\Foo;
            use function Fp\Collection\partitionOf;
            
            $result = partitionOf(
                [new Foo(1), new Bar(true)],
                false,
                Foo::class,
                Bar::class,
                Foo::class,
            );
        ';

        $this->assertBlockType($phpBlock, strtr(
            'array{list<Foo>, list<Bar>, list<Foo>, list<Bar|Foo>}',
            [
                'Foo' => Foo::class,
                'Bar' => Bar::class,
            ]
        ));
    }

    public function testPartitionOfWithTenClasses(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use Tests\Mock\Bar;
            use Tests\Mock\Foo;
            use function Fp\Collection\partitionOf;
            
            $result = partitionOf(
                [new Foo(1), new Bar(true)],
                false,
                Foo::class,
                Bar::class,
                Foo::class,
                Bar::class,
                Foo::class,
                Bar::class,
                Foo::class,
                Bar::class,
                Foo::class,
                Bar::class,
            );
        ';

        $this->assertBlockType($phpBlock, strtr(
            'array{list<Foo>, list<Bar>, list<Foo>, list<Bar>, list<Foo>, list<Bar>, list<Foo>, list<Bar>, list<Foo>, list<Bar>, list<Bar|Foo>}',
            [
                'Foo' => Foo::class,
                'Bar' => Bar::class,
            ]
        ));
    }
}
