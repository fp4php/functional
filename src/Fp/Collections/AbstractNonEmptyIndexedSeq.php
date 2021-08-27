<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractNonEmptySeq<TV>
 * @implements NonEmptyIndexedSeq<TV>
 */
abstract class AbstractNonEmptyIndexedSeq extends AbstractNonEmptySeq implements NonEmptyIndexedSeq
{

}
