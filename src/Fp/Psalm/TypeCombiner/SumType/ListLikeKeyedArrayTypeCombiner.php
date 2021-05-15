<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyList;

use function Fp\Cast\asList;
use function Fp\Collection\anyOf;
use function Fp\Collection\isNonEmptySequence;
use function Fp\Collection\keys;
use function Fp\Collection\map;
use function Fp\Collection\partition;
use function Fp\Collection\partitionOf;

/**
 * @internal
 */
class ListLikeKeyedArrayTypeCombiner implements TypeCombinerInterface
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
        $isListLike = [$this, 'isListLike'];

        [$keyedArrays, $atomics] = partitionOf($types, false, TKeyedArray::class);
        [$listLikeKeyedArrays, $keyedArrays] = partition($keyedArrays, $isListLike);

        $lists = map($listLikeKeyedArrays, function (TKeyedArray $a): TNonEmptyList {
            $a->is_list = true;
            return $a->getList();
        });

        return asList($lists, $keyedArrays, $atomics);
    }

    public function isListLike(TKeyedArray $a): bool
    {
        return isNonEmptySequence(keys($a->properties));
    }
}
