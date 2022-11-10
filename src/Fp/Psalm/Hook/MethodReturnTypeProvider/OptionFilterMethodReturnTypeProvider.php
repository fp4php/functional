<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\PredicateExtractor;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefineForEnum;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\first;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class OptionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Option::class, Some::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return proveTrue('filter' === $event->getMethodNameLowercase())
            ->flatMap(fn() => sequenceOptionT(
                fn() => Option::some(RefineForEnum::Value),
                fn() => PredicateExtractor::extract($event),
                fn() => Option::some($event->getContext()),
                fn() => proveOf($event->getSource(), StatementsAnalyzer::class),
                fn() => first($event->getTemplateTypeParameters() ?? [])
                    ->map(fn($value_type) => [Type::getArrayKey(), $value_type])
                    ->mapN(ctor(CollectionTypeParams::class)),
            ))
            ->mapN(ctor(RefinementContext::class))
            ->map(RefineByPredicate::for(...))
            ->map(fn(CollectionTypeParams $result) => new TGenericObject(Option::class, [$result->val_type]))
            ->map(fn(TGenericObject $result) => new Union([$result]))
            ->get();
    }
}
