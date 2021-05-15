<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner;

use Psalm\Type\Atomic;

/**
 * @see Atomic
 * @psalm-template A of Atomic
 */
interface TypeCombinerInterface
{
    /**
     * @psalm-param list<A> $types
     * @psalm-return list<A>
     */
    public function combine(array $types): array;
}
