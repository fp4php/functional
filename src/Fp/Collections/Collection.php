<?php

declare(strict_types=1);

namespace Fp\Collections;

use IteratorAggregate;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements IteratorAggregate<empty, TV>
 */
interface Collection extends IteratorAggregate
{

}
