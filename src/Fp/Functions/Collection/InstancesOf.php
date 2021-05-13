<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV of object
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 *
 * @psalm-return array<TK, TVO>
 */
function instancesOf(iterable $collection, string $fqcn): array
{
    /** @var array<TK, TVO> $instances */
    $instances = filter(
        $collection,
        fn(mixed $v): bool => is_a($v, $fqcn, true)
    );

    return $instances;
}
