<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\HashSet;

use function Fp\Cast\asGenerator;

/**
 * @template TV
 * @extends Monoid<HashSet<TV>>
 * @psalm-suppress InvalidTemplateParam
 */
class HashSetMonoid extends Monoid
{
    /**
     * @psalm-return HashSet<TV>
     */
    public function empty(): HashSet
    {
        return HashSet::collect([]);
    }

    /**
     * @psalm-param HashSet<TV> $lhs
     * @psalm-param HashSet<TV> $rhs
     * @psalm-return HashSet<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): HashSet
    {
        return HashSet::collect(asGenerator(function () use ($rhs, $lhs) {
            foreach ($lhs as $elem) {
                yield $elem;
            }
            foreach ($rhs as $elem) {
                yield $elem;
            }
        }));
    }
}

