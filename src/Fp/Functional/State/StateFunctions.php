<?php

declare(strict_types=1);

namespace Fp\Functional\State;

use Fp\Functional\Unit;

use function Fp\unit;

class StateFunctions
{
    /**
     * @psalm-pure
     * @template S
     * @template A
     * @param A $value
     * @return State<S, A>
     */
    public static function pure(mixed $value): State
    {
        return new State(function (mixed $state) use ($value) {
            return [$state, $value];
        });
    }

    /**
     * @psalm-pure
     * @template S
     * @template A
     * @param callable(S): A $f
     * @return State<S, A>
     */
    public static function inspect(callable $f): State
    {
        return new State(function (mixed $state) use ($f): array {
            /** @psalm-var S $state */
            return [$state, $f($state)];
        });
    }

    /**
     * @psalm-pure
     * @template S
     * @param S $state
     * @return State<S, Unit>
     */
    public static function set(mixed $state): State
    {
        return new State(fn() => [$state, unit()]);
    }

    /**
     * @psalm-pure
     * @template S
     * @param callable(): S $f
     * @return State<S, Unit>
     */
    public static function infer(callable $f): State
    {
        return new State(function (mixed $state) {
            /** @psalm-var S $state */
            return [$state, unit()];
        });
    }

    /**
     * @psalm-pure
     * @template S
     * @param callable(S): S $f
     * @return State<S, Unit>
     */
    public static function modify(callable $f): State
    {
        return new State(function (mixed $state) use ($f) {
            /** @psalm-var S $state */
            return [$f($state), unit()];
        });
    }
}
