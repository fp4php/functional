<?php

declare(strict_types=1);

namespace Fp\Collections;

use IteratorAggregate;

/**
 * @psalm-immutable
 * @template-covariant TK
 * @template-covariant TV
 */
interface NonEmptyCollection extends IteratorAggregate
{
    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyCollection<TKI, TVI>
     */
    public static function collect(iterable $source): NonEmptyCollection;
}
