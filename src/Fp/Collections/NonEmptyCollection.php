<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends Collection<TK, TV>
 */
interface NonEmptyCollection extends Collection
{
}
