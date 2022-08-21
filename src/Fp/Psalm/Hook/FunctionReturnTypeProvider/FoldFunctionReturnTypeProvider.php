<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Operations\FoldOperation;
use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\sequenceOption;

final class FoldFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Collection\fold')];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::some($event)
            ->flatMap(fn() => sequenceOption([
                PsalmApi::$args->getCallArgs($event)
                    ->flatMap(fn(ArrayList $args) => $args->lastElement())
                    ->pluck('type')
                    ->flatMap(GetCollectionTypeParams::value(...)),
                PsalmApi::$args->getCallArgs($event)
                    ->flatMap(fn(ArrayList $arrayList) => $arrayList->firstElement())
                    ->pluck('type')
                    ->map(PsalmApi::$types->asNonLiteralType(...)),
            ]))
            ->mapN(fn(Union $A, Union $TInit) => [
                new TGenericObject(FoldOperation::class, [$A, $TInit]),
            ])
            ->map(ctor(Union::class))
            ->get();
    }
}
