<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type\Atomic\TBool;

use function Fp\Cast\asList;
use function Fp\Collection\everyOf;
use function Fp\Collection\partition;

/**
 * @implements TypeCombinerInterface<TBool>
 */
class BoolTypeCombiner implements TypeCombinerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(array $types): bool
    {
        return !empty($types) && everyOf($types, TBool::class);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        [$parents, $children] = partition(
            $types,
            fn(TBool $t) => $t::class === TBool::class
        );

        return asList(empty($parents) ? $children : [$parents[0]]);
    }
}
