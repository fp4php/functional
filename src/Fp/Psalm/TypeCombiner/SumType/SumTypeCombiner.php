<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Functional\Tuple\Tuple8;
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
use function Fp\Evidence\proveListOf;

/**
 * @todo
 */
class SumTypeCombiner
{
    /**
     * @psalm-param list<Atomic> $atomics
     * @psalm-return Option<Tuple8<
     *     list<TBool>,
     *     list<TString>,
     *     list<TInt>,
     *     list<TFloat>,
     *     list<TArray>,
     *     list<TKeyedArray>,
     *     list<TList>,
     *     list<Atomic>
     * >>
     */
    private function partAtomicsByType(array $atomics)
    {
        $partitions = partition(
            $atomics,
            fn(Atomic $v) => $v instanceof TBool,
            fn(Atomic $v) => $v instanceof TString,
            fn(Atomic $v) => $v instanceof TInt,
            fn(Atomic $v) => $v instanceof TFloat,
            fn(Atomic $v) => $v instanceof TArray,
            fn(Atomic $v) => $v instanceof TKeyedArray,
            fn(Atomic $v) => $v instanceof TList,
        );

        return Option::do(function () use ($partitions) {
            $booleans = yield proveListOf(asList($partitions[0]), TBool::class);
            $strings = yield proveListOf(asList($partitions[1]), TString::class);
            $integers = yield proveListOf(asList($partitions[2]), TInt::class);
            $floats = yield proveListOf(asList($partitions[3]), TFloat::class);
            $arrays = yield proveListOf(asList($partitions[4]), TArray::class);
            $keyedArrays = yield proveListOf(asList($partitions[5]), TKeyedArray::class);
            $lists = yield proveListOf(asList($partitions[6]), TList::class);
            $tail = asList($partitions[7]);

            return Tuple8::ofArray([$booleans, $strings, $integers, $floats, $arrays, $keyedArrays, $lists, $tail]);
        });
    }

    /**
     * @psalm-param list<Atomic> $atomics
     */
    public function combine(array $atomics): Union
    {
        Option::do(function () use ($atomics) {
            $partitions = yield $this->partAtomicsByType($atomics);
            [$booleans, $strings, $integers, $floats, $arrays, $keyedArrays, $lists, $tail] = $partitions->toArray();

        });



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
}
