<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayAccess;

/**
 * @template-covariant TV
 * @extends Seq<TV>
 */
interface IndexedSeq extends Seq, ArrayAccess
{

}
