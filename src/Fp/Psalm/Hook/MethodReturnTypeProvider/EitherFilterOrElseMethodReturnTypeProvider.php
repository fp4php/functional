<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefineForEnum;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use PhpParser\Node\FunctionLike;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\first;
use function Fp\Collection\second;
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class EitherFilterOrElseMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Either::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return proveTrue(strtolower('filterOrElse') === $event->getMethodNameLowercase())
            ->flatMap(fn() => sequenceOption([
                fn() => Option::some(RefineForEnum::Value),
                fn() => first($event->getCallArgs())->pluck('value')->filterOf(FunctionLike::class),
                fn() => Option::some($event->getContext()),
                fn() => proveOf($event->getSource(), StatementsAnalyzer::class),
                fn() => second($event->getTemplateTypeParameters() ?? [])
                    ->map(fn($value_type) => [Type::getArrayKey(), $value_type])
                    ->mapN(ctor(CollectionTypeParams::class)),
            ]))
            ->mapN(ctor(RefinementContext::class))
            ->map(RefineByPredicate::for(...))
            ->map(fn(CollectionTypeParams $result) => new TGenericObject(Either::class, [
                self::getOutLeft($event),
                $result->val_type,
            ]))
            ->map(fn(TGenericObject $result) => new Union([$result]))
            ->get();
    }

    private static function getOutLeft(MethodReturnTypeProviderEvent $event): Union
    {
        return Type::combineUnionTypes(
            first($event->getTemplateTypeParameters() ?? [])
                ->getOrCall(fn() => Type::getNever()),
            second($event->getCallArgs())
                ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->filterOf([TClosure::class, TCallable::class])
                ->flatMap(fn(TClosure|TCallable $func) => Option::fromNullable($func->return_type))
                ->getOrCall(fn() => Type::getNever()),
        );
    }
}
