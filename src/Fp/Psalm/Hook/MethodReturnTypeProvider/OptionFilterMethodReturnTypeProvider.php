<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use PhpParser\Node\FunctionLike;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\first;
use function Fp\Collection\sequenceOption;
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
            ->flatMap(fn() => sequenceOption([
                Option::some(RefinementContext::FILTER_VALUE),
                first($event->getCallArgs())->pluck('value')->filterOf(FunctionLike::class),
                Option::some($event->getContext()),
                proveOf($event->getSource(), StatementsAnalyzer::class),
                first($event->getTemplateTypeParameters() ?? [])
                    ->map(fn($value_type) => [Type::getArrayKey(), $value_type])
                    ->mapN(ctor(CollectionTypeParams::class)),
            ]))
            ->mapN(ctor(RefinementContext::class))
            ->map(RefineByPredicate::for(...))
            ->map(fn(CollectionTypeParams $result) => new TGenericObject(Option::class, [$result->val_type]))
            ->map(fn(TGenericObject $result) => new Union([$result]))
            ->get();
    }
}
