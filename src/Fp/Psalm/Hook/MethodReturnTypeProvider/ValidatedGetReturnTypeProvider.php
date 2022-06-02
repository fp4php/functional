<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use function Fp\Collection\first;

final class ValidatedGetReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Validated::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return GenericGetReturnTypeProvider::getMethodReturnType(
            event: $event,
            for_class: Validated::class,
            to_negated: fn(TGenericObject $validated, string $possibility) => match (true) {
                str_starts_with($possibility, '!' . Valid::class) => new Union([
                    new TGenericObject(Invalid::class, [$validated->type_params[0]]),
                ]),
                str_starts_with($possibility, '!' . Invalid::class) => new Union([
                    new TGenericObject(Valid::class, [$validated->type_params[1]]),
                ]),
                default => new Union([$validated]),
            },
            to_return_type: fn(TGenericObject $generic) => match ($generic->value) {
                Invalid::class, Valid::class => first($generic->type_params),
                default => Option::none(),
            },
        );
    }
}
