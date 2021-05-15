<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;

use function Fp\Cast\asList;
use function Fp\Collection\anyOf;
use function Fp\Collection\map;
use function Fp\Collection\partition;
use function Fp\Collection\partitionOf;

class ArrayLikeKeyedArrayTypeCombiner implements TypeCombinerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(array $types): bool
    {
        return anyOf($types, TKeyedArray::class)
            && anyOf($types, TList::class);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $isArrayLike = [$this, 'isArrayLike'];

        [$keyedArrays, $atomics] = partitionOf($types, false, TKeyedArray::class);
        [$arrayLikeKeyedArrays, $keyedArrays] = partition($keyedArrays, $isArrayLike);

        $arrays = map($arrayLikeKeyedArrays, fn(TKeyedArray $a) => $a->getGenericArrayType());

        return asList($arrays, $keyedArrays, $atomics);
    }

    public function isArrayLike(TKeyedArray $a): bool
    {
        return $a->getGenericKeyType()->isSingle() && $a->getGenericValueType()->isSingle();
    }
}
