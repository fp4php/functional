<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SetChainableOps
{
    /**
     * Produces new set with given element included
     *
     * REPL:
     * >>> HashSet::collect([1, 1, 2])->updated(3)->toArray()
     * => [1, 2, 3]
     *
     * @template TVI
     * @param TVI $element
     * @return Set<TV|TVI>
     */
    public function updated(mixed $element): Set;

    /**
     * Produces new set with given element excluded
     *
     * REPL:
     * >>> HashSet::collect([1, 1, 2])->removed(2)->toArray()
     * => [1]
     *
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set;

    /**
     * Filter collection by condition
     *
     * REPL:
     * >>> HashSet::collect([1, 2, 2])->filter(fn($elem) => $elem > 1)->toArray()
     * => [2]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set;

    /**
     * Exclude null elements
     *
     * REPL:
     * >>> HashSet::collect([1, 1, null])->filterNotNull()->toArray()
     * => [1]
     *
     * @psalm-return Set<TV>
     */
    public function filterNotNull(): Set;

    /**
     * A combined {@see Set::map} and {@see Set::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * REPL:
     * >>> HashSet::collect(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toArray()
     * => [1, 2]
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function filterMap(callable $callback): Set;

    /**
     * REPL:
     * >>> HashSet::collect([2, 5, 5])->flatMap(fn($e) => [$e - 1, $e, $e, $e + 1])->toArray()
     * => [1, 2, 3, 4, 5, 6]
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function flatMap(callable $callback): Set;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> HashSet::collect([1, 2, 2])->map(fn($elem) => (string) $elem)->toArray()
     * => ['1', '2']
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return Set<TVO>
     */
    public function map(callable $callback): Set;

    /**
     * Call a function for every collection element
     *
     * REPL:
     * >>> HashSet::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray()
     * => [2, 3]
     *
     * @param callable(TV): void $callback
     * @psalm-return Set<TV>
     */
    public function tap(callable $callback): Set;

    /**
     * Returns every collection element except first
     *
     * REPL:
     * >>> HashSet::collect([1, 2, 3])->tail()->toArray()
     * => [2, 3]
     *
     * @psalm-return Set<TV>
     */
    public function tail(): Set;
}
