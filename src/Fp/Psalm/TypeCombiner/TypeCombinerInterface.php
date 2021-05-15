<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner;

use Psalm\Type\Atomic;

interface TypeCombinerInterface
{
    /**
     * @psalm-param list<Atomic> $types
     */
    public function supports(array $types): bool;

    /**
     * @psalm-param list<Atomic> $types
     * @psalm-return list<Atomic>
     */
    public function combine(array $types): array;
}
