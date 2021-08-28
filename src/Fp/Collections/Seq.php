<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends Collection<TV>
 * @extends SeqOps<TV>
 * @extends SeqCasts<TV>
 */
interface Seq extends Collection, SeqOps, SeqCasts
{

}
