<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\HashMap;
use Fp\Collections\IterableOnce;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends Monoid<HashMap<TK, TV>>
 * @psalm-immutable
 */
class HashMapMonoid extends Monoid
{
    /**
     * @psalm-return HashMap<TK, TV>
     */
    public function empty(): HashMap
    {
        return HashMap::collectPairs([]);
    }

    /**
     * @psalm-pure
     *
     * @psalm-param HashMap<TK, TV> $lhs
     * @psalm-param HashMap<TK, TV> $rhs
     *
     * @psalm-return HashMap<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): HashMap
    {
        return HashMap::collectPairs(IterableOnce::of(function () use ($rhs, $lhs) {
            foreach ($lhs as $pair) {
                yield $pair;
            }
            foreach ($rhs as $pair) {
                yield $pair;
            }
        }));
    }
}

