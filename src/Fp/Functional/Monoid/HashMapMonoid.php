<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\HashMap;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 * @extends Monoid<HashMap<TK, TV>>
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
     * @param HashMap<TK, TV> $lhs
     * @param HashMap<TK, TV> $rhs
     * @return HashMap<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): HashMap
    {
        return HashMap::collectPairs(asGenerator(function () use ($rhs, $lhs) {
            foreach ($lhs as $pair) {
                yield $pair;
            }
            foreach ($rhs as $pair) {
                yield $pair;
            }
        }));
    }
}

