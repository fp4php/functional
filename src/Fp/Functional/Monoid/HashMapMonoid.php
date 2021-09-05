<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\HashMap;
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
        return HashMap::collect([]);
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
        $source = function () use ($rhs, $lhs): Generator {
            foreach ($lhs as $pair) {
                yield $pair;
            }
            foreach ($rhs as $pair) {
                yield $pair;
            }
        };

        return HashMap::collect($source());
    }
}

