<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Union;

use function Fp\Cast\asArray;
use function Fp\Cast\asList;
use function Fp\Collection\every;
use function Fp\Collection\map;
use function Fp\Collection\some;

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
        return every($types, fn(Atomic $a) => $a instanceof TArray);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $hasPossiblyEmptyArray = some($types, fn(TArray $l) => $l::class === TArray::class);
        $keyTypeParams = asList(map($types, fn(TArray $list) => $list->type_params[0]));
        $valueTypeParams = asList(map($types, fn(TArray $list) => $list->type_params[1]));

        if (empty($keyTypeParams) || empty($valueTypeParams)) {
            return asList($types);
        }

        $combinedKeyTypeParams = Type::combineUnionTypeArray($keyTypeParams, null);
        $combinedValueTypeParams = Type::combineUnionTypeArray($valueTypeParams, null);

        $keyAtomics = asArray($combinedKeyTypeParams->getAtomicTypes(), false);
        $valueAtomics = asArray($combinedValueTypeParams->getAtomicTypes(), false);

        if (empty($keyAtomics) || empty($valueAtomics)) {
            return asList($types);
        }

        $reducedArray = $hasPossiblyEmptyArray
            ? new TArray([new Union($keyAtomics), new Union($valueAtomics)])
            : new TNonEmptyArray([new Union($keyAtomics), new Union($valueAtomics)]);

        return [$reducedArray];
    }
}
