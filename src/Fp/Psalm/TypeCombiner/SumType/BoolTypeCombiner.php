<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TBool;
use Psalm\Type\Atomic\TFalse;
use Psalm\Type\Atomic\TTrue;

use function Fp\Cast\asList;
use function Fp\Collection\every;
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
        return every($types, fn(Atomic $a) => $a instanceof TBool);
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
