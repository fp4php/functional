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
     * @psalm-template B of Atomic
     * @psalm-param list<B> $types
     */
    public function supports(array $types): bool;

    /**
     * @psalm-param list<A> $types
     * @psalm-return list<A>
     */
    public function combine(array $types): array;
}
