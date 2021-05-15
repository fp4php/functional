<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\filterOf;
use function Fp\Collection\map;
use function Fp\Collection\anyOf;
use function Fp\Collection\partitionOf;
use function Fp\Evidence\proveNonEmptyListOf;

/**
 * @internal
 */
class ArrayTypeCombiner implements TypeCombinerInterface
{
    public function supports(array $types): bool
    {
        return anyOf($types, TArray::class);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $combinedOption = Option::do(function () use ($types) {
            [$arrays, $notArrays] = partitionOf($types, false, TArray::class);
            $keyTypeParams = map($arrays, fn(TArray $list) => $list->type_params[0]);
            $valueTypeParams = map($arrays, fn(TArray $list) => $list->type_params[1]);

            $combinedKeyTypeParam = yield $this->combineTypeParams($keyTypeParams);
            $combinedValueTypeParam = yield $this->combineTypeParams($valueTypeParams);

            $combinedArray = anyOf($types, TArray::class, true)
                ? new TArray([$combinedKeyTypeParam, $combinedValueTypeParam])
                : new TNonEmptyArray([$combinedKeyTypeParam, $combinedValueTypeParam]);

            return asList([$combinedArray], $notArrays);
        });

        return $combinedOption->get() ?? $types;
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
