<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\every;
use function Fp\Collection\map;
use function Fp\Collection\anyOf;
use function Fp\Collection\partitionOf;
use function Fp\Evidence\proveNonEmptyListOf;
use function Fp\of;

/**
 * @internal
 */
class ArrayAndListTypeCombiner implements TypeCombinerInterface
{
    public function supports(array $types): bool
    {
        return anyOf($types, TArray::class)
            && anyOf($types, TList::class);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $combinedOption = Option::do(function () use ($types) {
            [$arrays, $lists, $notArrayOrListTypes] = partitionOf($types, false, TArray::class, TList::class);
            $arraysAndLists = asList($arrays, $lists);

            $keyTypeParams = map($arrays, fn(TArray $list) => $list->type_params[0]);
            $valueTypeParams = map(
                $arraysAndLists,
                fn(TArray|TList $a) => match (true) {
                    ($a instanceof TArray) => $a->type_params[1],
                    ($a instanceof TList) => $a->type_param,
                }
            );

            $combinedKeyTypeParam = yield $this->combineTypeParams($keyTypeParams);
            $combinedValueTypeParam = yield $this->combineTypeParams($valueTypeParams);

            $combined = $this->makeCombinedListOrArray(
                isEveryNonEmpty: every($arraysAndLists, [$this, 'ofNonEmpty']),
                keyTypeParam: $combinedKeyTypeParam,
                valueTypeParam: $combinedValueTypeParam
            );

            return asList([$combined], $notArrayOrListTypes);
        });

        return $combinedOption->get() ?? $types;
    }

    private function makeCombinedListOrArray(bool $isEveryNonEmpty, Union $keyTypeParam, Union $valueTypeParam): TArray|TList
    {
        if ($keyTypeParam->isEmpty() && !$valueTypeParam->isEmpty()) {
            return $isEveryNonEmpty
                ? new TNonEmptyList($valueTypeParam)
                : new TList($valueTypeParam);
        }

        return $isEveryNonEmpty
            ? new TNonEmptyArray([$keyTypeParam, $valueTypeParam])
            : new TArray([$keyTypeParam, $valueTypeParam]);
    }

    public function ofNonEmpty(TArray|TList $a): bool
    {
        return of($a, TNonEmptyList::class)
            || of($a, TNonEmptyArray::class);
    }

    /**
     * @template TK of array-key
     * @psalm-param iterable<TK, Union> $typeParams
     * @psalm-return Option<Union>
     */
    private function combineTypeParams(iterable $typeParams): Option
    {
        return Option::do(function () use ($typeParams) {
            $provenTypeParams = yield proveNonEmptyListOf($typeParams, Union::class);
            return Type::combineUnionTypeArray($provenTypeParams, null);
        });
    }
}
