<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Union;

use function Fp\Cast\asNonEmptyArray;
use function Fp\Collection\everyOf;
use function Fp\Collection\map;
use function Fp\Collection\some;
use function Fp\Collection\someOf;
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
        return everyOf($types, TArray::class, true);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $combinedOption = Option::do(function () use ($types) {
            $keyTypeParams = map($types, fn(TArray $list) => $list->type_params[0]);
            $valueTypeParams = map($types, fn(TArray $list) => $list->type_params[1]);

            $keyTypeParams = yield proveNonEmptyListOf($keyTypeParams, Union::class);
            $valueTypeParams = yield proveNonEmptyListOf($valueTypeParams, Union::class);

            $combinedKeyTypeParams = Type::combineUnionTypeArray($keyTypeParams, null);
            $combinedValueTypeParams = Type::combineUnionTypeArray($valueTypeParams, null);

            $keyAtomics = $combinedKeyTypeParams->getAtomicTypes();
            $valueAtomics = $combinedValueTypeParams->getAtomicTypes();

            $keyAtomics = yield asNonEmptyArray($keyAtomics, false);
            $valueAtomics = yield asNonEmptyArray($valueAtomics, false);

            $combinedArray = someOf($types, TArray::class, true)
                ? new TArray([new Union($keyAtomics), new Union($valueAtomics)])
                : new TNonEmptyArray([new Union($keyAtomics), new Union($valueAtomics)]);

            return [$combinedArray];

        });

        return $combinedOption->get() ?? $types;
    }
}
