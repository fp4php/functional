<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Union;

use function Fp\Collection\everyOf;
use function Fp\Collection\map;
use function Fp\Collection\anyOf;
use function Fp\Evidence\proveNonEmptyListOf;

/**
 * @implements TypeCombinerInterface<TArray>
 */
class ArrayTypeCombiner implements TypeCombinerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(array $types): bool
    {
        return !empty($types) && everyOf($types, TArray::class);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $combinedOption = Option::do(function () use ($types) {
            $keyTypeParams = map($types, fn(TArray $list) => $list->type_params[0]);
            $valueTypeParams = map($types, fn(TArray $list) => $list->type_params[1]);

            $combinedKeyTypeParam = yield $this->combineTypeParams($keyTypeParams);
            $combinedValueTypeParam = yield $this->combineTypeParams($valueTypeParams);

            $combinedArray = anyOf($types, TArray::class, true)
                ? new TArray([$combinedKeyTypeParam, $combinedValueTypeParam])
                : new TNonEmptyArray([$combinedKeyTypeParam, $combinedValueTypeParam]);

            return [$combinedArray];
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
