<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TString;

use function Fp\Cast\asList;
use function Fp\Collection\every;
use function Fp\Collection\partition;

/**
 * @implements TypeCombinerInterface<TString>
 */
class StringTypeCombiner implements TypeCombinerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(array $types): bool
    {
        return every($types, fn(Atomic $a) => $a instanceof TString);
    }

    /**
     * @inheritdoc
     */
    public function combine(array $types): array
    {
        [$parents, $children] = partition(
            $types,
            fn(TString $t) => $t::class === TString::class
        );

        return asList(empty($parents) ? $children : [$parents[0]]);
    }
}
