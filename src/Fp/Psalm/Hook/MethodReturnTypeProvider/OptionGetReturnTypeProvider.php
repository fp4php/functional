<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\TypeFromPsalmAssertionResolver;
use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Storage\Assertion;
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
            to_negated: fn(TGenericObject $option, Assertion $possibility) => match (true) {
                $possibility->isNegation() && $possibility->getAtomicType()?->getId() === Some::class => new Union([
                    new TNamedObject(None::class),
                ]),
                $possibility->isNegation() && $possibility->getAtomicType()?->getId() === None::class => new Union([
                    new TGenericObject(Some::class, [$option->type_params[0]]),
                ]),
                default => new Union([$option]),
            },
            to_return_type: fn(TGenericObject $generic) => match ($generic->value) {
                Some::class => first($generic->type_params),
                Option::class => first($generic->type_params)->map(function(Union $union) {
                    return PsalmApi::$types->asNullable($union);
                }),
                default => Option::some(Type::getNull())
            },
        );
    }
}
