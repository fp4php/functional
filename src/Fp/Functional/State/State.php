<?php

declare(strict_types=1);

namespace Fp\Functional\State;

use Closure;
use Fp\Functional\Unit;

use function Fp\unit;

/**
 * @template S
 * @template A
 * @psalm-yield A
 * @psalm-immutable
 */
final class State
{
    /**
     * @param Closure(S): array{S, A} $func
     */
    public function __construct(private Closure $func) { }

    /**
     * @psalm-pure
     * @template SS
     * @template AA
     * @param Closure(SS): array{SS, AA} $func
     * @return static<SS, AA>
     */
    public static function of(Closure $func): self
    {
        return new self($func);
    }

    /**
     * @psalm-template B
     * @param callable(A): B $f
     * @psalm-return self<S, B>
     */
    public function map(callable $f): self
    {
        return self::of(function (mixed $state) use ($f): array {
            /** @psalm-var S $state */
            $stateUp = $state;
            [$stateDown, $valueDown] = ($this->func)($stateUp);

            return [$stateDown, $f($valueDown)];
        });
    }

    /**
     * @psalm-template B
     * @param callable(A): State<S, B> $f
     * @psalm-return State<S, B>
     */
    public function flatMap(callable $f): self
    {
        return self::of(function (mixed $state) use ($f): array {
            /** @psalm-var S $state0 */
            $state0 = $state;

            [$state1, $value1] = ($this->func)($state0);
            [$state2, $value2] = ($f($value1)->func)($state1);

            return [$state2, $value2];
        });
    }

    /**
     * @template AA
     * @param callable(S): AA $f
     * @return State<S, AA>
     */
    public function inspect(callable $f): State
    {
        return self::of(function(mixed $state) use ($f) {
            /** @psalm-var S $stateUp */
            $stateUp = $state;
            $stateDown = ($this->func)($stateUp)[0];

            return [$stateDown, $f($stateDown)];
        });
    }

    /**
     * @return State<S, S>
     */
    public function get(): State
    {
        return $this->inspect(fn(mixed $state): mixed => $state);
    }

    /**
     * @param S $state
     * @return State<S, Unit>
     */
    public function set(mixed $state): State
    {
        $stateDown = $state;

        return self::of(function (mixed $state) use ($stateDown) {
            /** @psalm-var S $stateUp */
            $stateUp = $state;

            ($this->func)($stateUp);

            return [$stateDown, unit()];
        });
    }

    /**
     * @psalm-pure
     * @param callable(S): S $f
     * @return State<S, Unit>
     */
    public function modify(callable $f): State
    {
        return new State(function (mixed $state) use ($f) {
            /** @psalm-var S $stateUp */
            $stateUp = $state;
            $stateDown = ($this->func)($stateUp)[0];

            return [$f($stateDown), unit()];
        });
    }

    /**
     * @param S $state
     * @return A
     */
    public function run(mixed $state): mixed
    {
        return ($this->func)($state)[1];
    }
}
