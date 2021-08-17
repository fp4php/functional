<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template TK of (object|scalar)
 * @template-covariant TV
 * @psalm-immutable
 * @extends Collection<array{TK, TV}>
 * @extends MapOps<TK, TV>
 */
interface Map extends Collection, MapOps
{
    /**
     * @return list<array{TK, TV}>
     */
    public function toArray(): array;

    /**
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * REPL:
     * >>> HashMap::collect([['a', 1], ['b', 2]])
     * => HashMap('a' -> 1, 'b' -> 2)
     *
     * @psalm-pure
     * @template TKI of (object|scalar)
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collect(iterable $source): self;

    /**
     * REPL:
     * >>> HashMap::collectIterable(['a' => 1, 'b' => 2])
     * => HashMap('a' -> 1, 'b' -> 2)
     *
     * @psalm-pure
     * @template TKI of array-key
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectIterable(iterable $source): self;
}
