<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractNonEmptySeq<TV>
 * @implements NonEmptyLinearSeq<TV>
 */
abstract class AbstractNonEmptyLinearSeq extends AbstractNonEmptySeq implements NonEmptyLinearSeq
{

}
