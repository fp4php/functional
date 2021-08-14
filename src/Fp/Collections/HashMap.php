<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @implements Map<TK, TV>
 * @psalm-type hash = string
 */
class HashMap implements Map
{
    /**
     * @var array<hash, array{TK, TV}>
     */
    private array $hashTable = [];

    /**
     * @param iterable<array{TK, TV}> $source
     */
    public function __construct(iterable $source)
    {
        foreach ($source as $pair) {
            $hash = is_object($pair[0]) ? spl_object_hash($pair[0]) : (string) $pair[0];
            $this->hashTable[$hash] = $pair;
        }
    }

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collect(iterable $source): self
    {
        return new self($source);
    }

    /**
     * @psalm-pure
     * @template TKI of array-key
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectIterable(iterable $source): self
    {
        $buffer = []; // TODO

        /** @psalm-suppress ImpureMethodCall */
        foreach ($source as $idx => $elem) {
            $buffer[] = [$idx, $elem];
        }

        return self::collect($buffer);
    }

    /**
     * @return ArrayIterator<hash, array{TK, TV}>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->hashTable);
    }
}
