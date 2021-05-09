<?php

declare(strict_types=1);

namespace Fp\Function\Collection;

use Fp\Functional\Option\Option;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param null|callable(TV, TK): bool $predicate
 *
 * @psalm-return Option<TV>
 */
function first(iterable $collection, ?callable $predicate = null): Option
{
    if (is_null($predicate)) {
        return head($collection);
    }

    $first = null;

    foreach ($collection as $index => $element) {
        if (call_user_func($predicate, $element, $index)) {
            $first = $element;
            break;
        }
    }

    return Option::of($first);
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 *
 * @psalm-return Option<TVO>
 */
function firstInstanceOf(iterable $collection, string $fqcn): Option
{
    /** @var Option<TVO> $first */
    $first = first(
        $collection,
        fn(mixed $v): bool => is_a($v, $fqcn, true)
    );

    return $first;
}
