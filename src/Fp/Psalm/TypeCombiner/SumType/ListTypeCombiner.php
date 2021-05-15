<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Union;

use function Fp\Collection\anyOf;
use function Fp\Collection\filterOf;
use function Fp\Collection\map;
use function Fp\Evidence\proveNonEmptyListOf;

class ListTypeCombiner implements TypeCombinerInterface
{
    public function supports(array $types): bool
    {
        return anyOf($types, TList::class);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        $combinedOption = Option::do(function () use ($types) {
            $lists = filterOf($types, TList::class);
            $typeParams = map($lists, fn(TList $list) => $list->type_param);
            $combinedTypeParam = yield $this->combineTypeParams($typeParams);

            $combinedArray = anyOf($types, TList::class, true)
                ? new TList($combinedTypeParam)
                : new TNonEmptyList($combinedTypeParam);

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
