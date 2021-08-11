<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyCollection<empty, TV>
 * @extends NonEmptySeqOps<TV>
 */
interface NonEmptySeq extends NonEmptyCollection, NonEmptySeqOps
{

}
