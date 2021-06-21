<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Functional\Option\Option;
use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TEmpty;
use Psalm\Type\Atomic\TList;

use function Fp\Cast\asList;
use function Fp\Collection\any;
use function Fp\Collection\first;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;
use function Fp\of;

/**
 * @internal
 */
class EmptyArrayTypeCombiner implements TypeCombinerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(array $types): bool
    {
        return any($types, [$this, 'isEmptyArray']);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        return asList($types, [new TList(Type::getEmpty())]);
    }

    public function isEmptyArray(Atomic $a): bool
    {
        $isEmptyOption = Option::do(function () use ($a) {
            $arrayAtomic = yield proveOf($a, TArray::class, false);
            [$keyUnion, $valueUnion] = $arrayAtomic->type_params;
            yield proveTrue(1 === count($keyUnion->getAtomicTypes()));
            yield proveTrue(1 === count($valueUnion->getAtomicTypes()));
            $keyAtomic = yield first($keyUnion->getAtomicTypes());
            $valueAtomic = yield first($valueUnion->getAtomicTypes());

            return of($keyAtomic, TEmpty::class) && of($valueAtomic, TEmpty::class);
        });

        return $isEmptyOption->getOrElse(false);
    }
}
