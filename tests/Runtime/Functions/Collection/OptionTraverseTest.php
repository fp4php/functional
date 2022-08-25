<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
use function Fp\Collection\sequenceOption;
use function Fp\Collection\traverseOption;

final class OptionTraverseTest extends TestCase
{
    public function testTraverse(): void
    {
        /** @psalm-var list<int> $c */
        $c = [1, 2];

        $this->assertEquals(
            Option::some($c),
            traverseOption($c, fn(int $v) => $v < 3
                ? Option::some($v)
                : Option::none())
        );

        $this->assertEquals(
            Option::none(),
            traverseOption($c, fn(int $v) => $v < 2
                ? Option::some($v)
                : Option::none()),
        );
    }

    public function testSequence(): void
    {
        $this->assertEquals(
            Option::some([1, 2]),
            sequenceOption([
                Option::some(1),
                Option::some(2),
            ])
        );

        $this->assertEquals(
            Option::none(),
            sequenceOption([
                Option::some(1),
                Option::none(),
            ])
        );

        $this->assertEquals(
            Option::none(),
            sequenceOption([
                Option::none(),
                Option::some(1),
            ])
        );
    }

    public function testLazySequence(): void
    {
        $this->assertEquals(
            Option::some([1, 2]),
            sequenceOption([
                fn() => Option::some(1),
                fn() => Option::some(2),
            ]),
        );

        $this->assertEquals(
            Option::some([1, 2]),
            sequenceOption([
                Option::some(1),
                fn() => Option::some(2),
            ]),
        );

        $this->assertEquals(
            Option::some(['fst' => 1, 'snd' => 2]),
            sequenceOption([
                'fst' => fn() => Option::some(1),
                'snd' => fn() => Option::some(2),
            ]),
        );

        $this->assertEquals(
            Option::some(['fst' => 1, 'snd' => 2]),
            sequenceOption([
                'fst' => Option::some(1),
                'snd' => fn() => Option::some(2),
            ]),
        );
    }
}
