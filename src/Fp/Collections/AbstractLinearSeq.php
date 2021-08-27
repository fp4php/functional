<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractSeq<TV>
 * @implements LinearSeq<TV>
 */
abstract class AbstractLinearSeq extends AbstractSeq implements LinearSeq
{

}
