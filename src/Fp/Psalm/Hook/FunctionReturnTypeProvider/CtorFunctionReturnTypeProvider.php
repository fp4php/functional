<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Storage\MethodStorage;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TLiteralClassString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Evidence\of;
use function Fp\Callable\ctor;
use function Fp\Collection\at;

final class CtorFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Callable\ctor')];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->head()
            ->pluck('type')
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->flatMap(of(TLiteralClassString::class))
            ->pluck('value')
            ->map(ctor(TNamedObject::class))
            ->map(fn(TNamedObject $class) => [
                new TClosure(
                    value: 'Closure',
                    params: PsalmApi::$classlikes->getStorage($class)
                        ->flatMap(fn(ClassLikeStorage $storage) => at($storage->methods, '__construct'))
                        ->map(fn(MethodStorage $method) => $method->params)
                        ->getOrElse([]),
                    return_type: new Union([$class]),
                )
            ])
            ->map(ctor(Union::class))
            ->get();
    }
}
