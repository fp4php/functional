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
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyCollection<TKI, TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(iterable $source): NonEmptyCollection;

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyCollection<TKI, TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyCollection;

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param non-empty-array<TKI, TVI>|NonEmptyCollection<TKI, TVI> $source
     * @return NonEmptyCollection<TKI, TVI>
     */
    public static function collectNonEmpty(iterable $source): NonEmptyCollection;
}
