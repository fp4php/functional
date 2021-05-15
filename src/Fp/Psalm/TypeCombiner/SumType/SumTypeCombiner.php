<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Tuple\Tuple3;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TBool;
use Psalm\Type\Atomic\TFloat;
use Psalm\Type\Atomic\TInt;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Atomic\TString;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\isNonEmptySequence;
use function Fp\Collection\isSequence;
use function Fp\Collection\keys;
use function Fp\Collection\map;
use function Fp\Collection\partition;
use function Fp\Collection\partitionOf;

/**
 * @todo
 */
class SumTypeCombiner
{
    private BoolTypeCombiner $boolTypeCombiner;
    private StringTypeCombiner $stringTypeCombiner;
    private IntTypeCombiner $intTypeCombiner;
    private FloatTypeCombiner $floatTypeCombiner;
    private ArrayTypeCombiner $arrayTypeCombiner;
    private ListTypeCombiner $listTypeCombiner;

    public function __construct()
    {
        $this->boolTypeCombiner = new BoolTypeCombiner();
        $this->stringTypeCombiner = new StringTypeCombiner();
        $this->intTypeCombiner = new IntTypeCombiner();
        $this->floatTypeCombiner = new FloatTypeCombiner();
        $this->arrayTypeCombiner = new ArrayTypeCombiner();
        $this->listTypeCombiner = new ListTypeCombiner();
    }

    /**
     * @psalm-param list<Atomic> $atomics
     */
    public function combine(array $atomics): Union
    {
        $partitions = partitionOf(
            $atomics,
            false,
            TBool::class,
            TString::class,
            TInt::class,
            TFloat::class,
            TKeyedArray::class,
            TArray::class,
            TList::class,
        );

        [$bools, $strings, $ints, $floats, $keyedArrays, $arrays, $lists, $tail] = $partitions;

        // keyed
        [$castedLists, $castedArrays, $keyedArrays] = $this->tryCastToListOrArray($keyedArrays)->toArray();

        $listsWithLikes = [...asList($castedLists), ...$lists];
        $arraysWithLikes = [...asList($castedArrays), ...$arrays];

        $combinedBools = $this->boolTypeCombiner->combine($bools);
        $combinedStrings = $this->stringTypeCombiner->combine($strings);
        $combinedInts = $this->intTypeCombiner->combine($ints);
        $combinedFloats = $this->floatTypeCombiner->combine($floats);
        $combinedArrays = $this->arrayTypeCombiner->combine($arraysWithLikes);
        $combinedLists = $this->listTypeCombiner->combine($listsWithLikes);

        $reducedAtomics = [
            ...$combinedBools,
            ...$combinedStrings,
            ...$combinedInts,
            ...$combinedFloats,
            ...$combinedArrays,
            ...$combinedLists,
            ...asList($keyedArrays),
            ...asList($tail),
        ];

        return new Union($reducedAtomics);
    }

    /**
     * @psalm-param list<TKeyedArray> $keyedArrays
     * @psalm-return Tuple3<list<TNonEmptyList>, list<TArray>, list<TKeyedArray>>
     */
    private function tryCastToListOrArray(array $keyedArrays): Tuple3
    {
        [$listLikes, $arrayLikes, $tail] = partition(
            $keyedArrays,
            fn(TKeyedArray $a) => isNonEmptySequence(keys($a->properties)),
            fn(TKeyedArray $a) => $a->getGenericKeyType()->isSingle() && $a->getGenericValueType()->isSingle()
        );

        $lists = map($listLikes, function (TKeyedArray $a): TNonEmptyList {
            $a->is_list = true;
            return $a->getList();
        });

        $arrays = map($arrayLikes, function (TKeyedArray $a): TArray {
            return $a->getGenericArrayType();
        });

        return Tuple3::ofArray([
            asList($lists), asList($arrays), asList($tail)
        ]);
    }
}
