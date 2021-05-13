<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 *
 * @psalm-return bool
 */
function every(iterable $collection, callable $predicate, bool $strict = true): bool
{
    $result = !$strict;

    foreach ($collection as $index => $element) {
        $result = true;

        if (!call_user_func($predicate, $element, $index)) {
            $result = false;
            break;
        }
    }

    return $result;
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO

 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 *
 * @psalm-return bool
 */
function everyOf(iterable $collection, string $fqcn, bool $strict = true): bool
{
    return every(
        $collection,
        fn(mixed $v) => is_a($v, $fqcn, true),
        $strict
    );
}
