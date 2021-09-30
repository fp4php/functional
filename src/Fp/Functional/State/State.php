<?php

declare(strict_types=1);

namespace Fp\Functional\State;

use Closure;
use Fp\Functional\Trampoline\Done;
use Fp\Functional\Trampoline\More;
use Fp\Functional\Trampoline\Trampoline;
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
     * @param Closure(S): Trampoline<array{S, A}> $runS
     */
    public function __construct(private Closure $runS) { }

    /**
     * Fabric method for State dataclass instantiation
     *
     * @psalm-pure
     * @template SS
     * @template AA
     * @param Closure(SS): Trampoline<array{SS, AA}> $func
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
        return self::of(function (mixed $state) use ($f): Trampoline {
            /** @psalm-var S $state */
            $stateUp = $state;

            return ($this->runS)($stateUp)
                ->flatMap(fn($pair) => Done::of([$pair[0], $f($pair[1])]));
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

            return ($this->runS)($stateUp)
                ->flatMap(fn($pair) => Done::of([$pair[0], $f($pair[0])]));
        });
    }

    /**
     * @psalm-pure
     * @template SS
     * @template AA
     * @param callable(SS): AA $f
     * @return State<SS, AA>
     */
    public static function inspectState(callable $f): State
    {
        return new State(function (mixed $state) use ($f): Trampoline {
            /** @psalm-var SS $state */
            return Done::of([$state, $f($state)]);
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

            return ($this->runS)($stateUp)
                ->flatMap(fn($pair) => Done::of([$stateDown, unit()]));
        });
    }

    /**
     * @psalm-pure
     * @template SS
     * @param SS $state
     * @return State<SS, Unit>
     */
    public static function setState(mixed $state): State
    {
        return new State(fn() => Done::of([$state, unit()]));
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

            return ($this->runS)($stateUp)
                ->flatMap(fn($pair) => Done::of([$f($pair[0]), unit()]));
        });
    }

    /**
     * @psalm-pure
     * @template SS
     * @param callable(SS): SS $f
     * @return State<SS, Unit>
     */
    public static function modifyState(callable $f): State
    {
        return new State(function (mixed $state) use ($f) {
            /** @psalm-var SS $state */
            return Done::of([$f($state), unit()]);
        });
    }

    /**
     * @psalm-pure
     * @template SS
     * @param callable(): SS $f
     * @return State<SS, Unit>
     */
    public static function infer(callable $f): State
    {
        return new State(function (mixed $state) {
            /** @psalm-var SS $state */
            return Done::of([$state, unit()]);
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
        return ($this->runS)($state)->run();
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
        return ($this->runS)($state)->run()[1];
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
        return ($this->runS)($state)->run()[0];
    }


    /**
     * @psalm-template B
     * @param callable(A): State<S, B> $f
     * @psalm-return State<S, B>
     */
    public function flatMap(callable $f): self
    {
        return self::of(function (mixed $state) use ($f): Trampoline {
            /** @psalm-var S $state0 */
            $state0 = $state;

            return More::of(fn() => ($this->runS)($state0))
                ->flatMap(fn($pair) => More::of(fn() => ($f($pair[1])->runS)($pair[0])))
                    ->flatMap(fn($pair) => Done::of($pair));
        });
    }

    /**
     * Stack unsafe
     *
     * @psalm-pure
     * @template TState
     * @template TReturn
     * @template TSend
     * @psalm-param callable(): Generator<int, State<TState, mixed>, TSend, TReturn> $computation
     * @psalm-return self<TState, TReturn>
     * @psalm-suppress ImpureMethodCall, ImpureFunctionCall
     */
    public static function do(callable $computation): self
    {
        $gen = $computation();
        $cur = $gen->current();
        return self::doWalk($gen, $cur)->map(fn() => $gen->getReturn());
    }

    /**
     * @param Generator<State> $gen
     */
    private static function doWalk(Generator $gen, State $currentState): State
    {
        return $currentState->flatMap(function ($val) use ($currentState, $gen) {
            $gen->send($val);
            $nextState = $gen->current();

            return !$gen->valid()
                ? $currentState
                : self::doWalk($gen, $nextState);
        });
    }
}
