<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * Fast {@see Seq::at()} and {@see IndexedSeq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends Seq<TV>
 */
interface IndexedSeq extends Seq
{

}
