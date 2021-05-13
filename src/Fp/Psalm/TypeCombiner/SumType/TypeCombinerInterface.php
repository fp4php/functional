<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner;

use Psalm\Type\Atomic;

/**
 * @see Atomic
 * @psalm-template T of Atomic
 */
interface TypeCombinerInterface
{
    /**
     * @psalm-param list<T> $types
     */
    public function supports(array $types): bool;

    /**
     * @psalm-param list<T> $types
     * @psalm-return list<T>
     */
    public function combine(array $types): array;
}
