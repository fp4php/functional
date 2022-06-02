<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\Option;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use function Fp\Collection\first;

final class EitherGetReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Either::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return GenericGetReturnTypeProvider::getMethodReturnType(
            event: $event,
            for_class: Either::class,
            to_negated: fn(TGenericObject $either, string $possibility) => match (true) {
                str_starts_with($possibility, '!' . Right::class) => new Union([
                    new TGenericObject(Left::class, [$either->type_params[0]]),
                ]),
                str_starts_with($possibility, '!' . Left::class) => new Union([
                    new TGenericObject(Right::class, [$either->type_params[1]]),
                ]),
                default => new Union([$either]),
            },
            to_return_type: fn(TGenericObject $generic) => match ($generic->value) {
                Left::class, Right::class => first($generic->type_params),
                default => Option::none(),
            },
        );
    }
}
