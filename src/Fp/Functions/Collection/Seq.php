<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyCollection;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\Seq;

/**
 * Collect iterable with default sequence implementation
 *
 * REPL:
 * >>> seq([1, 2, 3]);
 * => LinkedList(1, 2, 3)
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return Seq<TV>
 */
function seq(iterable $collection): Seq
{
    return LinkedList::collect($collection);
}

/**
 * Collect iterable with default non-empty sequence implementation
 *
 * REPL:
 * >>> nonEmptySeq([1, 2, 3]);
 * => NonEmptyLinkedList(1, 2, 3)
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param non-empty-array<TK, TV>|NonEmptyCollection<TK, TV> $collection
 * @psalm-return NonEmptySeq<TV>
 */
function nonEmptySeq(iterable $collection): NonEmptySeq
{
    return NonEmptyLinkedList::collect($collection);
}
