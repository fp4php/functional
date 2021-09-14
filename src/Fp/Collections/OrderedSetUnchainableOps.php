<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface OrderedSetUnchainableOps
{
    /**
     * Find first element which satisfies the condition
     *
     * REPL:
     * >>> HashSet::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get()
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
     * >>> HashSet::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get()
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
     * >>> HashSet::collect([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get()
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
     * >>> HashSet::collect([1, 2])->head()->get()
     * => 1
     *
     * @psalm-return Option<TV>
     */
    public function head(): Option;

    /**
     * Returns first collection element
     * Alias for {@see SetOps::head}
     *
     * REPL:
     * >>> HashSet::collect([1, 2])->firstElement()->get()
     * => 1
     *
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option;

    /**
     * Returns last collection element
     *
     * REPL:
     * >>> HashSet::collect([1, 2])->lastElement()->get()
     * => 2
     *
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option;
}
