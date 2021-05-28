<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Psalm\Psalm;
use Fp\Psalm\TypeCombiner\SumType\SumTypeCombiner;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;

use function Fp\Collection\last;
use function Fp\Evidence\proveTrue;


class EitherGetOrElseMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Either::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
            yield proveTrue('getorelse' === $event->getMethodNameLowercase());
            $lower = yield self::getLowerBoundary($event);
            $upper = yield self::getUpperBoundary($event);
            return SumTypeCombiner::combineUnions([$lower, $upper]);
        })->get();
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getLowerBoundary(MethodReturnTypeProviderEvent $event): Option
    {
        return Option::do(function() use ($event) {
            $template_type_parameters = yield Option::fromNullable($event->getTemplateTypeParameters());
            return yield last($template_type_parameters);
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getUpperBoundary(MethodReturnTypeProviderEvent $event): Option
    {
        return ($first_arg_type = Psalm::getFirstArgType($event))
            ->flatMap(fn(Union $arg_type) => Psalm::getFirstCallableType($arg_type))
            ->flatMap(fn(TCallable|TClosure $f) => Option::fromNullable($f->return_type))
            ->orElse(fn() => $first_arg_type);
    }
}
