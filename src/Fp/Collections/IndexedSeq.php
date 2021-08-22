<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * Fast {@see Seq::at()} and {@see IndexedSeq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends Seq<TV>
 */
interface IndexedSeq extends Seq
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
