<?php

declare(strict_types=1);

namespace Fp\Functional\State;

use Closure;
use Fp\Functional\Unit;

use Generator;

use function Fp\unit;

/**
 * State dataclass encapsulates
 * function from some state to new modified state
 * and some value which represents computation
 *
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
     * Fabric method for State dataclass instantiation
     *
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
     * Map value. State will not be changed
     *
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
     * Inspect current state
     * and compute some value based on this state.
     *
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
     * Copy current state to computed value
     *
     * @return State<S, S>
     */
    public function get(): State
    {
        return $this->inspect(fn(mixed $state): mixed => $state);
    }

    /**
     * Set current state and discard value
     *
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
     * Modify current state and discard value
     *
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
     * Run with the provided initial state value
     *
     * @param S $state
     * @return array{S, A}
     */
    public function run(mixed $state): array
    {
        return ($this->func)($state);
    }

    /**
     * Run with the provided initial state value and return the final value
     * (discarding the final state).
     *
     * @param S $state
     * @return A
     */
    public function runA(mixed $state): mixed
    {
        return ($this->func)($state)[1];
    }

    /**
     * Run with the provided initial state value and return the final state
     * (discarding the final value)
     *
     * @param S $state
     * @return S
     */
    public function runS(mixed $state): mixed
    {
        return ($this->func)($state)[0];
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
     * @template TState
     * @template TReturn
     * @template TSend
     * @psalm-param callable(): Generator<int, State<TState, mixed>, TSend, TReturn> $computation
     * @psalm-return self<TState, TReturn>
     */
    public static function do(callable $computation): self {
        $gen = $computation();
        $cur = $gen->current();
        return self::doWalk($gen, $cur)->map(fn() => $gen->getReturn());
    }

    /**
     * @template TState
     * @template TReturn
     * @template TSend
     * @psalm-param callable(): Generator<int, State<mixed, mixed>, TSend, TReturn> $computation
     * @psalm-param TState $initState
     * @psalm-return self<TState, TReturn>
     */
    public static function does(mixed $initState, callable $computation, int $maxNestingLevel = 100): self {
        $gen = $computation();

        $cur = StateFunctions::pure(unit());

        while ($gen->valid()) {
            $cur = $gen->current();
            $cur = self::doWalk($gen, $cur, $maxNestingLevel);
            $pair = $cur->run($initState);

            /** @psalm-var TState $initState */
            $initState = $pair[0];

            $cur = StateFunctions::set($pair[0])->map(fn(): mixed => $pair[1]);
        }

        return $cur->map(fn() => $gen->getReturn());
    }

    /**
     * @psalm-param null|callable(State, int): array{State, int} $guard
     */
    private static function doWalk(Generator $gen, State $currentState, int $maxLevel = 0, int $curLevel = 0): State
    {
        return $currentState->flatMap(function ($val) use ($maxLevel, $curLevel, $currentState, $gen) {
            $gen->send($val);

            /** @var State $nextState */
            $nextState = $gen->current();

            return !$gen->valid() || ($maxLevel > 0 && $curLevel >= $maxLevel)
                ? $currentState
                : self::doWalk($gen, $nextState, $maxLevel, ++$curLevel);
        });
    }
}
