<?php

declare(strict_types=1);

namespace Fp\Collection;

use function Fp\of;

/**
 * Returns true if every collection element satisfies the condition
 * false otherwise
 *
 * REPL:
 * >>> every([1, 2], fn(int $v) => $v === 1);
 * => false
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 *
 * @psalm-return bool
 */
function every(iterable $collection, callable $predicate): bool
{
    $result = true;

    foreach ($collection as $index => $element) {
        if (!call_user_func($predicate, $element, $index)) {
            $result = false;
            break;
        }
    }

    return $result;
}

/**
 * Returns true if every collection element is of given class
 * false otherwise
 *
 * REPL:
 * >>> everyOf([1, new Foo()], Foo::class);
 * => false
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO

 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-return bool
 */
function everyOf(iterable $collection, string $fqcn, bool $invariant = false): bool
{
    return every(
        $collection,
        fn(mixed $v) => of($v, $fqcn, $invariant)
    );
}
