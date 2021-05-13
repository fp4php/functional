<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TBool;
use Psalm\Type\Atomic\TFloat;
use Psalm\Type\Atomic\TInt;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TString;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\map;
use function Fp\Collection\partition;

/**
 * @todo
 */
class SumTypeCombiner
{
    /**
     * @psalm-param list<Atomic> $atomics
     */
    public function combine(array $atomics): Union
    {
        [
            $booleans,
            $strings,
            $integers,
            $floats,
            $arrays,
            $keyedArrays,
            $lists,
            $tail,
        ] = partition(
            $atomics,
            fn(Atomic $v) => $v instanceof TBool,
            fn(Atomic $v) => $v instanceof TString,
            fn(Atomic $v) => $v instanceof TInt,
            fn(Atomic $v) => $v instanceof TFloat,
            fn(Atomic $v) => $v instanceof TArray,
            fn(Atomic $v) => $v instanceof TKeyedArray,
            fn(Atomic $v) => $v instanceof TList,
        );

        $keyedArrays = instancesOf($keyedArrays, TKeyedArray::class);

        [$listLikeKeyedArrays, $arrayLikeKeyedArrays, $keyedArrays] = partition(
            $keyedArrays,
            fn(TKeyedArray $a) => self::isList($a),
            fn(TKeyedArray $a) => $a->getGenericKeyType()->isSingle() && $a->getGenericValueType()->isSingle()
        );

        $listLikeKeyedArrays = map($listLikeKeyedArrays, function (TKeyedArray $a) {
            $a->is_list = true;
            return $a->getList();
        });

        $arrayLikeKeyedArrays = map($arrayLikeKeyedArrays, function (TKeyedArray $a) {
            return $a->getGenericArrayType();
        });

        $lists = [...array_values($listLikeKeyedArrays), ...array_values($lists)];
        $arrays = [...array_values($arrayLikeKeyedArrays), ...array_values($arrays)];

        $booleans = self::reduceBooleans(instancesOf($booleans, TBool::class));
        $strings = self::reduceStrings(instancesOf($strings, TString::class));
        $integers = self::reduceIntegers(instancesOf($integers, TInt::class));
        $floats = self::reduceFloats(instancesOf($floats, TFloat::class));
        $arrays = self::reduceArrays(instancesOf($arrays, TArray::class));
        $lists = self::reduceLists(instancesOf($lists, TList::class));

        $reducedAtomics = [
            ...$booleans,
            ...$strings,
            ...$integers,
            ...$floats,
            ...$arrays,
            ...$lists,
            ...asList($keyedArrays),
            ...asList($tail),
        ];

        return new Union($reducedAtomics);
    }


    private function isList(TKeyedArray $keyedArray): bool
    {
        $isList = true;
        $previousKey = -1;

        foreach ($keyedArray->properties as $key => $value) {
            if ($key - $previousKey !== 1) {
                $isList = false;
                break;
            }
        }

        return $isList;
    }
}
