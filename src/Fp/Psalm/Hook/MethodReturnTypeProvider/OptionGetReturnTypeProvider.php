<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\TypeFromPsalmAssertionResolver;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;
use function Fp\Collection\first;

final class OptionGetReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Option::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return TypeFromPsalmAssertionResolver::getMethodReturnType(
            event: $event,
            for_class: Option::class,
            to_negated: fn(TGenericObject $option, string $possibility) => match (true) {
                str_starts_with($possibility, '!' . Some::class) => new Union([
                    new TNamedObject(None::class),
                ]),
                str_starts_with($possibility, '!' . None::class) => new Union([
                    new TGenericObject(Some::class, [$option->type_params[0]]),
                ]),
                default => new Union([$option]),
            },
            to_return_type: fn(TGenericObject $generic) => match ($generic->value) {
                Some::class => first($generic->type_params),
                Option::class => first($generic->type_params)->map(function(Union $union) {
                    $nullable = clone $union;
                    $nullable->addType(new Type\Atomic\TNull());

                    return $nullable;
                }),
                default => Option::some(Type::getNull())
            },
        );
    }
}
