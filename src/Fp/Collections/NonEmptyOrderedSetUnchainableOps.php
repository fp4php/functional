<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptyOrderedSetUnchainableOps
{
    /**
     * Find first element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->first(fn($elem) => $elem > 1)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 0, 2])->last(fn($elem) => $elem > 0)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get()
     * => Foo(2)
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Return first collection element
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->head()
     * => 1
     *
     * @psalm-return TV
     */
    public function head(): mixed;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySetOps::head}
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->firstElement()
     * => 1
     *
     * @psalm-return TV
     */
    public function firstElement(): mixed;

    /**
     * Returns last collection element
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->lastElement()
     * => 2
     *
     * @psalm-return TV
     */
    public function lastElement(): mixed;
}
