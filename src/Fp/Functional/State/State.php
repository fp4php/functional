<?php

declare(strict_types=1);

namespace Fp\Functional\State;

use Closure;


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
    public function __construct(private Closure $func)
    {
    }

    /**
     * @psalm-template B
     * @param callable(A): B $f
     * @psalm-return self<S, B>
     */
    public function map(callable $f): self
    {
        return new self(function (mixed $state) use ($f): array {
            /** @psalm-var S $state */
            return [$state, $f(($this->func)($state)[1])];
        });
    }

    /**
     * @psalm-template B
     * @param callable(A): self<S, B> $f
     * @psalm-return self<S, B>
     */
    public function flatMap(callable $f): self
    {
        return new self(function (mixed $state) use ($f): array {
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
        return new self(function(mixed $state) use ($f) {
            /** @psalm-var S $state0 */
            $state0 = $state;
            $state1 = ($this->func)($state0)[0];

            return [$state1, $f($state1)];
        });
    }

    /**
     * @template AA
     * @return State<S, S>
     */
    public function get(): State
    {
        return $this->inspect(fn(mixed $state): mixed => $state);
    }

    /**
     * @psalm-pure
     * @param callable(S): S $f
     * @return State<S, A>
     */
    public function modify(callable $f): State
    {
        return new State(function (mixed $state) use ($f) {
            /** @psalm-var S $state0 */
            $state0 = $state;

            [$state1, $value1] = ($this->func)($state0);

            return [$f($state1), $value1];
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
