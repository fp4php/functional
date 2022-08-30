<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Evidence\proveNonEmptyList;

final class SeparatedToEitherMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Separated::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return proveNonEmptyList($event->getTemplateTypeParameters() ?? [])
            ->map(fn(array $templates) => new TGenericObject(Either::class, $templates))
            ->map(fn(TGenericObject $object) => new Union([$object]))
            ->get();
    }
}
