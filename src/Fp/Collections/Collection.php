<?php

declare(strict_types=1);

namespace Fp\Collections;

use IteratorAggregate;

/**
 * @template-covariant TK
 * @template-covariant TV
 */
interface Collection extends IteratorAggregate
{
    /**
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return Collection<TKI, TVI>
     */
    public static function collect(iterable $source): self;
}
