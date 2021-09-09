<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterExpressionAnalysis;

use Fp\Collections\HashMap;
use Fp\Collections\StaticStorage;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\classOf;
use function Fp\Collection\at;
use function Fp\Collection\firstOf;
use function Fp\Collection\head;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;
use function Fp\objectOf;

final class MapGetMethodCallAnalysis implements AfterExpressionAnalysisInterface
{
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        Option::do(function () use ($event) {
            $call = yield proveOf($event->getExpr(), MethodCall::class);
            yield proveTrue(objectOf($call->name, Identifier::class));
            yield proveTrue('get' === $call->name->name);

            $var_union = yield Psalm::getNodeUnion($call->var, $event->getStatementsSource());
            $keyed_array = yield Psalm::getUnionTGenericObjectSingleAtomic($var_union)
                ->filter(fn(TGenericObject $object) => classOf($object->value, HashMap::class))
                ->flatMap(fn(TGenericObject $object) => Option::fromNullable($object->extra_types))
                ->flatMap(fn($extra) => firstOf($extra, TGenericObject::class))
                ->filter(fn(TGenericObject $object) => classOf($object->value, StaticStorage::class))
                ->flatMap(fn(TGenericObject $object) => Psalm::getUnionTKeyedArraySingleAtomic($object->type_params[0]));

            $arg_union = yield Psalm::getArgUnion(yield head($call->args), $event->getStatementsSource());
            $key_literal = yield Psalm::getUnionSingleIntOrStringLiteralValue($arg_union);
            $value_union = yield at($keyed_array->properties, $key_literal);

            $event->getStatementsSource()->getNodeTypeProvider()->setType($event->getExpr(), new Union([
                new TGenericObject(Some::class, [$value_union])
            ]));
        });

        return null;
    }
}
