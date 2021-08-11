<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SeqOps
{
    /**
     * @psalm-return Option<TV>
     */
    public function head(): Option;

//    /**
//     * @template TVO
//     * @psalm-param callable(TV): bool $predicate
//     */
//    public function any(callable $predicate): bool;

//    /**
//     * @template TVO
//     * @psalm-param callable(TV): TVO $callback
//     * @psalm-return Seq<TVO>
//     */
//    public function map(callable $callback): Seq;
}
