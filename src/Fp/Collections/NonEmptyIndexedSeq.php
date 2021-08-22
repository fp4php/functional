<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * Fast {@see NonEmptySeq::at()} and {@see NonEmptyIndexedSeq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySeq<TV>
 */
interface NonEmptyIndexedSeq extends NonEmptySeq
{
    /**
     * Find element by its index (Starts from zero).
     * Returns None if there is no such collection element.
     *
     * REPL:
     * >>> ArrayList::collect([1, 2])->at(1)->get()
     * => 2
     *
     * Alias for {@see Seq::at()}
     *
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option;
}
