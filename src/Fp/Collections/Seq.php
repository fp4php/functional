<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends Collection<empty, TV>
 * @extends SeqOps<TV>
 */
interface Seq extends Collection, SeqOps
{

}
