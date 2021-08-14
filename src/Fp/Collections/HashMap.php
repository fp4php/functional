<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Error;
use Generator;

/**
 * @template TK of (object|scalar)
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

            $hash = (string) $this->computeKeyHash($pair[0]);

            if (!isset($this->hashTable[$hash])) {
                $this->hashTable[$hash] = Nil::getInstance();
            }

            $this->hashTable[$hash] = $this->hashTable[$hash]
                ->filter(function (array $p) use ($pair) {
                    return match (true) {
                        $pair[0] instanceof HashContract && $p[0] instanceof HashContract => !($pair[0]->equals($p[0])),
                        default => !($this->hashEquals($pair[0], $p[0])),
                    };
                })
                ->prepend($pair);
        }
    }

    /**
     * @param object|scalar $lhs
     * @param object|scalar $rhs
     * @return bool
     */
    private function hashEquals(mixed $lhs, mixed $rhs): bool
    {
        return $this->computeKeyHash($lhs) === $this->computeKeyHash($rhs);
    }

    /**
     * @param object|scalar $key
     * @return string|int|float|bool
     */
    private function computeKeyHash(object|string|int|float|bool $key): string|int|float|bool
    {
        /** @psalm-suppress ImpureMethodCall */
        return match (true) {
            $key instanceof HashContract => $key->hashCode(),
            is_object($key) => spl_object_hash($key),
            default => $key,
        };
    }

    /**
     * @psalm-pure
     * @template TKI of (object|scalar)
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
