<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends Collection<array{TK, TV}>
 * @extends MapOps<TK, TV>
 * @extends MapCasts<TK, TV>
 */
interface Map extends Collection, MapOps, MapCasts
{

}
