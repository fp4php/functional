<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Generator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @implements Map<TK, TV>
 * @psalm-type hash = string
 */
final class HashMap implements Map
{
    /**
     * @var array<hash, Seq<array{TK, TV}>>
     */
    private array $hashTable = [];

    /**
     * @param iterable<array{TK, TV}> $source
     */
    private function __construct(iterable $source)
    {
        foreach ($source as $pair) {
            $hash = is_object($pair[0]) ? spl_object_hash($pair[0]) : (string) $pair[0];

            /** @var Seq<array{TK, TV}> $nil */
            $nil = Nil::getInstance();

            if (!isset($this->hashTable[$hash])) {
                $this->hashTable[$hash] = $nil;
            }

            $this->hashTable[$hash] = $this->hashTable[$hash]
                ->filter(fn(array $p) => $pair[0] !== $p[0])
                ->prepend($pair);
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
        $pairSource = function() use ($source): Generator {
            foreach ($source as $idx => $elem) {
                yield [$idx, $elem];
            }
        };

        /** @psalm-suppress ImpureFunctionCall */
        return self::collect($pairSource());
    }

    /**
     * @return ArrayIterator<int, array{TK, TV}>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * @return list<array{TK, TV}>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this->hashTable as $bucket) {
            foreach ($bucket as $pair) {
                $buffer[] = $pair;
            }
        }

        return $buffer;
    }
}
