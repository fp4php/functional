<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\State;

use Fp\Functional\State\State;
use Fp\Functional\Trampoline\Done;
use PHPUnit\Framework\TestCase;

final class StateTest extends TestCase
{
    public function testFlatMap(): void
    {
        /** @var non-empty-array<string, int> $state */
        $state = [];

        $state1 = State::of(function (array $s) {
            $s['a'] = 1;
            return Done::of([$s, ['a', 1]]);
        });
        $state2 = State::of(function (array $s) {
            $s['b'] = 2;
            return Done::of([$s, ['b', 2]]);
        });
        $state3 = State::of(function (array $s) {
            $s['c'] = 3;
            return Done::of([$s, ['c', 3]]);
        });

        $actual = $state1
            ->flatMap(fn($pair1) => $state2
                ->flatMap(fn($pair2) => $state3
                    ->map(fn($pair3) => [$pair1, $pair2, $pair3]
            )))
            ->run($state);

        $this->assertEquals(
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [['a', 1], ['b', 2], ['c', 3]]
            ],
            $actual
        );
    }

    public function testDo(): void
    {
        /** @var non-empty-array<string, int> $init */
        $init = [];

        $state = State::do(function() {
            $state1 = State::of(function (array $s) {
                $s['a'] = 1;
                return Done::of([$s, ['a', 1]]);
            });
            $state2 = State::of(function (array $s) {
                $s['b'] = 2;
                return Done::of([$s, ['b', 2]]);
            });
            $state3 = State::of(function (array $s) {
                $s['c'] = 3;
                return Done::of([$s, ['c', 3]]);
            });

            $pair1 = yield $state1;
            $pair2 = yield $state2;
            $pair3 = yield $state3;

            return [$pair1, $pair2, $pair3];
        });

        $this->assertEquals(
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [['a', 1], ['b', 2], ['c', 3]]
            ],
            $state->run($init)
        );
    }
}
