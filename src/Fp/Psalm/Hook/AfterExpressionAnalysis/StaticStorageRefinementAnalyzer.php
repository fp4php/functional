<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterExpressionAnalysis;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\StaticStorage;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\classOf;
use function Fp\Collection\at;
use function Fp\Collection\firstOf;
use function Fp\Collection\head;
use function Fp\Evidence\proveNonEmptyArrayOf;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;
use function Fp\objectOf;

final class StaticStorageRefinementAnalyzer implements AfterExpressionAnalysisInterface
{
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        Option::do(function () use ($event) {
            $call = yield proveOf($event->getExpr(), MethodCall::class);
            yield proveTrue(objectOf($call->name, Identifier::class));
            yield proveTrue('get' === $call->name->name);

            $var_union = yield Psalm::getNodeUnion($call->var, $event->getStatementsSource());
            $keyed_array = yield Psalm::getUnionSingleAtomicOf($var_union, TNamedObject::class)
                ->filter(fn(TNamedObject $object) => classOf($object->value, Map::class)
                    || classOf($object->value, NonEmptyMap::class)
                )
                ->flatMap(fn(TNamedObject $obj) => Option::fromNullable($obj->extra_types))
                ->flatMap(fn($extra) => firstOf($extra, TGenericObject::class))
                ->filter(fn(TGenericObject $obj) => classOf($obj->value, StaticStorage::class))
                ->map(fn(TGenericObject $obj) => $obj->type_params[0])
                ->flatMap(fn(Union $union) => self::adaptToDict($union));

            $arg_union = yield Psalm::getArgUnion(yield head($call->args), $event->getStatementsSource());
            $key_literal = yield Psalm::getUnionSingleIntOrStringLiteralValue($arg_union);
            $value_union = yield at($keyed_array->properties, $key_literal);

            $event->getStatementsSource()->getNodeTypeProvider()->setType($event->getExpr(), new Union([
                new TGenericObject(Some::class, [$value_union])
            ]));
        });

        return null;
    }

    /**
     * @psalm-return Option<TKeyedArray>
     */
    private static function adaptToDict(Union $union): Option
    {
        return self::convertPairsToDict($union)
            ->orElse(function () use ($union) {
                return Psalm::getUnionSingleAtomicOf($union, TKeyedArray::class);
            });
    }

    /**
     * @psalm-return Option<TKeyedArray>
     */
    private static function convertPairsToDict(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $pairs = yield Psalm::getUnionSingleAtomicOf($union, TKeyedArray::class)
                ->filter(fn(TKeyedArray $a) => $a->is_list)
                ->map(fn(TKeyedArray $a) => ArrayList::collect($a->properties));

            $properties = $pairs
                ->filterMap(fn(Union $u) => Psalm::getUnionSingleAtomicOf($u, TKeyedArray::class))
                ->map(fn(TKeyedArray $a) => $a->properties)
                ->filter(fn($props) => 2 === count($props))
                ->filter(fn($props) => array_key_exists(0, $props))
                ->filter(fn($props) => array_key_exists(1, $props))
                ->filterMap(function ($props) {
                    return Psalm::getUnionSingleIntOrStringLiteralValue($props[0])
                        ->map(fn($key) => [$key, $props[1]]);
                })
                ->fold([], function (array $acc, $pair) {
                    $acc[$pair[0]] = $pair[1];
                    return $acc;
                });

            return new TKeyedArray(yield proveNonEmptyArrayOf($properties, Union::class));
        });
    }
}
