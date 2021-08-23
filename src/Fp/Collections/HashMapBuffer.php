<?php

declare(strict_types=1);

namespace Fp\Collections;

use ReflectionClass;

use function Fp\Reflection\getReflectionClass;

/**
 * Provides fast update operation
 *
 * @template TK
 * @template TV
 * @psalm-type hash = string
 */
final class HashMapBuffer
{
    /**
     * @var array<string, list<array{TK, TV}>>
     */
    private array $hashTable = [];

    /**
     * @param TK $key
     * @param TV $value
     * @return self<TK, TV>
     * @psalm-suppress PropertyTypeCoercion
     */
    public function update(mixed $key, mixed $value): self
    {
        $hash = (string) HashComparator::computeHash($key);

        if (!isset($this->hashTable[$hash])) {
            $this->hashTable[$hash] = [];
        }

        $replacedIdx = -1;

        foreach ($this->hashTable[$hash] as $idx => [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $replacedIdx = $idx;
                $this->hashTable[$hash][$idx][1] = $value;
            }
        }

        if ($replacedIdx < 0) {
            $this->hashTable[$hash][] = [$key, $value];
        }

        return $this;
    }

    /**
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        /** @var HashMap<TK, TV> */
        return getReflectionClass(HashMap::class)
            ->map(function (ReflectionClass $class) {
                $constructor = $class->getConstructor();
                $object = $class->newInstanceWithoutConstructor();
                $constructor?->setAccessible(true);
                $constructor?->invoke($object, $this->hashTable);

                return $object;
            })
            ->toOption()
            ->getUnsafe();
    }
}
