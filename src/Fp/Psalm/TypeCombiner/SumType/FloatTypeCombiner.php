<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type\Atomic\TFloat;

use function Fp\Cast\asList;
use function Fp\Collection\partition;

/**
 * @implements TypeCombinerInterface<TFloat>
 */
class FloatTypeCombiner implements TypeCombinerInterface
{
    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        [$parents, $children] = partition(
            $types,
            fn(TFloat $t) => $t::class === TFloat::class
        );

        return asList(empty($parents) ? $children : [$parents[0]]);
    }
}
