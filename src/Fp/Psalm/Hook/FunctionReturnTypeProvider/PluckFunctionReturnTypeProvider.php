<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\Psalm\Util\ListChecker;
use Fp\Psalm\Util\Pluck\PluckPropertyTypeResolver;
use Fp\Psalm\Util\Pluck\PluckResolveContext;
use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Evidence\of;
use function Fp\Callable\ctor;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\proveOf;

final class PluckFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Collection\pluck')];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::some(PsalmApi::$args->getCallArgs($event))
            ->flatMap(fn(ArrayList $args) => sequenceOptionT(
                fn() => $args->lastElement()
                    ->pluck('type')
                    ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                    ->flatMap(of(TLiteralString::class)),
                fn() => $args->firstElement()
                    ->pluck('type')
                    ->flatMap(GetCollectionTypeParams::value(...))
                    ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                    ->flatMap(fn($atomic) => proveOf($atomic, [TNamedObject::class, TKeyedArray::class])),
                fn() => Option::some($event->getStatementsSource()),
                fn() => Option::some($event->getCodeLocation()),
            ))
            ->mapN(ctor(PluckResolveContext::class))
            ->flatMap(PluckPropertyTypeResolver::resolve(...))
            ->flatMap(fn(Union $result) => PsalmApi::$args->getCallArgs($event)
                ->head()
                ->pluck('type')
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->map(fn(Type\Atomic $atomic) => [
                    match (true) {
                        $atomic instanceof TKeyedArray && ListChecker::isNonEmptyList($atomic) => Type::getNonEmptyListAtomic($result),
                        $atomic instanceof TKeyedArray && ListChecker::isList($atomic) => Type::getListAtomic($result),
                        $atomic instanceof TNonEmptyArray => new TNonEmptyArray([self::getArrayKey($event), $result]),
                        default => new TArray([self::getArrayKey($event), $result]),
                    },
                ])
            )
            ->map(ctor(Union::class))
            ->get();
    }

    private static function getArrayKey(FunctionReturnTypeProviderEvent $event): Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->head()
            ->pluck('type')
            ->flatMap(GetCollectionTypeParams::key(...))
            ->getOrCall(fn() => Type::getArrayKey());
    }
}
