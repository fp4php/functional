<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;

use function Fp\of;

/**
 * Find first element which satisfies the condition
 *
 * REPL:
 * >>> first([1, 2], fn(int $v): bool => $v === 2)->get()
 * => 1
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
        if ($predicate($element, $index)) {
            $first = $element;
            break;
        }
    }

    return Option::fromNullable($first);
}

/**
 * Find first element of given class
 *
 * REPL:
 * >>> firstOf([1, new Foo(1), new Foo(2)], Foo::class)->get()
 * => Foo(1)
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
    /** @var Option<TVO> */
    return first(
        $collection,
        fn(mixed $v): bool => of($v, $fqcn, $invariant)
    );
}
