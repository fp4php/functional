<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\any;

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
        $union = new Union([$a]);
        return $union->hasEmptyArray();
    }

    public function isNonEmptyArray(Atomic $a): bool
    {
        $union = new Union([$a]);
        return $union->hasEmptyArray();
    }
}
