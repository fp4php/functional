<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Collections\Folding;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use function Fp\Collection\first;
use function Fp\Collection\second;
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class FoldMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [ArrayList::class, Folding::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return self::foldingWithoutLiteralInit($event)
            ->orElse(fn() => self::foldReturnType($event))
            ->get();
    }

    /**
     * @return Option<Union>
     */
    private static function foldReturnType(MethodReturnTypeProviderEvent $event): Option
    {
        return proveTrue(Folding::class === $event->getFqClasslikeName())
            ->flatMap(
                fn() => sequenceOption([
                    second($event->getTemplateTypeParameters() ?? []),
                    proveOf($event->getStmt(), MethodCall::class)
                        ->map(fn(MethodCall $call) => $call->getArgs())
                        ->flatMap(fn(array $args) => first($args))
                        ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
                        ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomicOf(TClosure::class, $type))
                        ->flatMap(fn(TClosure $closure) => Option::fromNullable($closure->return_type))
                        ->map(fn(Union $type) => PsalmApi::$types->asNonLiteralType($type))
                        ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomic($type))
                        ->map(fn(Atomic $atomic) => match (true) {
                            $atomic instanceof Atomic\TNonEmptyList => new Atomic\TList($atomic->type_param),
                            $atomic instanceof Atomic\TNonEmptyArray => new Atomic\TArray($atomic->type_params),
                            default => $atomic,
                        })
                        ->map(fn(Atomic $atomic) => new Union([$atomic]))
                ])
            )
            ->map(fn($types) => Type::combineUnionTypeArray($types, PsalmApi::$codebase));
    }

    /**
     * @return Option<Union>
     */
    private static function foldingWithoutLiteralInit(MethodReturnTypeProviderEvent $event): Option
    {
        return proveTrue('folding' === $event->getMethodNameLowercase())
            ->flatMap(
                fn() => sequenceOption([
                    first($event->getTemplateTypeParameters() ?? []),
                    proveOf($event->getStmt(), MethodCall::class)
                        ->map(fn(MethodCall $call) => $call->getArgs())
                        ->flatMap(fn(array $args) => first($args))
                        ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
                        ->map(fn(Union $type) => PsalmApi::$types->asNonLiteralType($type)),
                ])
            )
            ->map(fn(array $type_params) => new Union([
                new TGenericObject(Folding::class, $type_params),
            ]));
    }
}
