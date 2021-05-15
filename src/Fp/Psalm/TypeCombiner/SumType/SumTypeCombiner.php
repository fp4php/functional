<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCombiner\SumType;

use Fp\Psalm\TypeCombiner\TypeCombinerInterface;
use Psalm\Internal\Type\TypeCombiner;
use Psalm\Type\Atomic;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\flatMap;

/**
 * @internal
 */
class SumTypeCombiner
{
    /**
     * @psalm-param non-empty-list<Union> $unions
     */
    public static function combineUnions(array $unions): Union
    {
        /** @var non-empty-list<Atomic> $atomics */
        $atomics = asList(flatMap($unions, fn(Union $u) => $u->getAtomicTypes()));

        return self::combineAtomics($atomics);
    }

    /**
     * @psalm-param non-empty-list<Atomic> $atomics
     */
    public static function combineAtomics(array $atomics): Union
    {
        $pipeline = [
            new EmptyArrayTypeCombiner(),
            new ListLikeKeyedArrayTypeCombiner(),
            new ArrayLikeKeyedArrayTypeCombiner(),
            new ListTypeCombiner(),
            new ArrayTypeCombiner(),
            new ArrayAndListTypeCombiner(),
        ];

        $combinedAtomics = self::passThroughPipeline($pipeline, $atomics);

        return empty($combinedAtomics)
            ? TypeCombiner::combine($atomics)
            : TypeCombiner::combine($combinedAtomics);
    }

    /**
     * @psalm-param list<TypeCombinerInterface> $pipeline
     * @psalm-param list<Atomic> $types
     * @psalm-return list<Atomic>
     */
    private static function passThroughPipeline(array $pipeline, array $types): array
    {
        $combinedTypes = $types;

        foreach ($pipeline as $pipe) {
            if ($pipe->supports($combinedTypes)) {
                $combinedTypes = $pipe->combine($combinedTypes);
            }
        }

        return $combinedTypes;
    }
}
