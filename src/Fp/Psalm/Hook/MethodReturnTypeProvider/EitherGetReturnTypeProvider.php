<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Assertion;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\PsalmApi;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Type\Atomic;
use Psalm\Type\Union;

use function Fp\Collection\first;
use function Fp\Collection\firstMap;
use function Fp\Evidence\proveOf;

final class EitherGetReturnTypeProvider implements AfterExpressionAnalysisInterface
{
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        return self::getGetCallExpr($event)
            ->flatMap(fn($call) => self::inferTypeFromGetCall($event, $call))
            ->tap(fn($type) => PsalmApi::$types->setType($event, $event->getExpr(), $type))
            ->map(fn() => null)->get();
    }

    /**
     * @return Option<MethodCall>
     */
    private static function getGetCallExpr(AfterExpressionAnalysisEvent $event): Option
    {
        return proveOf($event->getExpr(), MethodCall::class)
            ->filter(fn(MethodCall $c) => proveOf($c->name, Identifier::class)
                ->map(fn($id) => $id->name === 'get')
                ->getOrElse(false));
    }

    /**
     * @return Option<Union>
     */
    private static function inferTypeFromGetCall(AfterExpressionAnalysisEvent $event, MethodCall $call): Option
    {
        return PsalmApi::$types->getType($event, $call->var)
            ->flatMap(fn(Union $type) => firstMap(
                $type->getAtomicTypes(),
                fn(Atomic $atomic) => Option::firstT(
                    fn() => proveOf($atomic, Atomic\TGenericObject::class)
                        ->filter(fn(Atomic\TGenericObject $g) => $g->value === Right::class)
                        ->filter(fn(Atomic\TGenericObject $g) => array_key_exists(Assertion::class, $g->getIntersectionTypes()))
                        ->flatMap(fn(Atomic\TGenericObject $g) => first($g->type_params)),
                    fn() => proveOf($atomic, Atomic\TGenericObject::class)
                        ->filter(fn(Atomic\TGenericObject $g) => $g->value === Left::class)
                        ->filter(fn(Atomic\TGenericObject $g) => array_key_exists(Assertion::class, $g->getIntersectionTypes()))
                        ->flatMap(fn(Atomic\TGenericObject $g) => first($g->type_params)),
                ),
            ));
    }
}
