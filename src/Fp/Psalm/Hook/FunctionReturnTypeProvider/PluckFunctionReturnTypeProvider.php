<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\Psalm\Util\Pluck\PluckPropertyTypeResolver;
use Fp\Psalm\Util\Pluck\PluckResolveContext;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveOf;

class PluckFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Collection\pluck')];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn(ArrayList $args) => sequenceOption([
                $args->lastElement()
                    ->pluck('type')
                    ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                    ->filterOf(TLiteralString::class),
                $args->firstElement()
                    ->pluck('type')
                    ->flatMap(GetCollectionTypeParams::value(...))
                    ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                    ->flatMap(fn($atomic) => proveOf($atomic, [TNamedObject::class, TKeyedArray::class])),
                Option::some($event->getStatementsSource()),
                Option::some($event->getCodeLocation()),
            ]))
            ->mapN(ctor(PluckResolveContext::class))
            ->flatMap(PluckPropertyTypeResolver::resolve(...))
            ->map(fn(Union $result) => [
                match (true) {
                    self::itWas(TNonEmptyList::class, $event) => new TNonEmptyList($result),
                    self::itWas(TList::class, $event) => new TList($result),
                    self::itWas(TNonEmptyArray::class, $event) => new TNonEmptyArray([self::getArrayKey($event), $result]),
                    default => new TArray([self::getArrayKey($event), $result]),
                },
            ])
            ->map(ctor(Union::class))
            ->get();
    }

    private static function getArrayKey(FunctionReturnTypeProviderEvent $event): Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn(ArrayList $args) => $args->head())
            ->pluck('type')
            ->flatMap(GetCollectionTypeParams::key(...))
            ->getOrCall(fn() => Type::getArrayKey());
    }

    private static function itWas(string $class, FunctionReturnTypeProviderEvent $event): bool
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn(ArrayList $args) => $args->head())
            ->pluck('type')
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->map(fn(Type\Atomic $atomic) => $atomic instanceof $class)
            ->getOrElse(false);
    }
}
