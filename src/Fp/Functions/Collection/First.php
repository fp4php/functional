<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;

use function Fp\of;

/**
 * Find first element which satisfies the condition
 *
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
 * Find first element of given class
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-return Option<TVO>
 */
function firstOf(iterable $collection, string $fqcn, bool $invariant = false): Option
{
    /** @var Option<TVO> $first */
    $first = first(
        $collection,
        fn(mixed $v): bool => of($v, $fqcn, $invariant)
    );

    return $first;
}
