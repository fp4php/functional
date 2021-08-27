<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * Fast {@see NonEmptySeq::at()} and {@see NonEmptyIndexedSeq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySeq<TV>
 */
interface NonEmptyIndexedSeq extends NonEmptySeq
{

}
