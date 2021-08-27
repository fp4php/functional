<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractSeq<TV>
 * @implements IndexedSeq<TV>
 */
abstract class AbstractIndexedSeq extends AbstractSeq implements IndexedSeq
{

}
