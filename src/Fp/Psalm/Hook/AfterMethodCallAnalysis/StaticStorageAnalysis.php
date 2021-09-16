<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterMethodCallAnalysis;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\Set;
use Fp\Collections\StaticStorage;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Collection\head;
use function Fp\Evidence\proveNonEmptyArrayOf;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class StaticStorageAnalysis implements AfterMethodCallAnalysisInterface
{
    private const COLLECT = 'collect';
    private const COLLECT_UNSAFE = 'collectUnsafe';
    private const COLLECT_NON_EMPTY = 'collectNonEmpty';
    private const COLLECT_PAIRS = 'collectPairs';
    private const COLLECT_PAIRS_UNSAFE = 'collectPairsUnsafe';
    private const COLLECT_PAIRS_NON_EMPTY = 'collectPairsNonEmpty';

    /**
     * @return Set<string>
     */
    private static function getAllowedMethods(): Set
    {
        static $set = null;

        if (is_null($set)) {
            $set = HashSet::collect([
                HashMap::class . '::' . self::COLLECT,
                HashMap::class . '::' . strtolower(self::COLLECT_PAIRS),
                NonEmptyHashMap::class . '::' . self::COLLECT,
                NonEmptyHashMap::class . '::' . strtolower(self::COLLECT_PAIRS),
                NonEmptyHashMap::class . '::' . strtolower(self::COLLECT_UNSAFE),
                NonEmptyHashMap::class . '::' . strtolower(self::COLLECT_NON_EMPTY),
                NonEmptyHashMap::class . '::' . strtolower(self::COLLECT_PAIRS),
                NonEmptyHashMap::class . '::' . strtolower(self::COLLECT_PAIRS_UNSAFE),
                NonEmptyHashMap::class . '::' . strtolower(self::COLLECT_PAIRS_NON_EMPTY),
            ]);
        }

        /** @var Set<string> */
        return $set;
    }

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        Option::do(function () use ($event) {
            yield proveTrue(self::getAllowedMethods()->contains($event->getMethodId()));

            $arg = yield head($event->getExpr()->args);
            $union = yield Psalm::getArgUnion($arg, $event->getStatementsSource());

            $method_name = yield proveOf($event->getExpr(), StaticCall::class)
                ->flatMap(fn(StaticCall $call) => proveOf($call->name, Identifier::class))
                ->map(fn(Identifier $id) => $id->name);

            $storage = self::filterPairCollector($method_name, $union)->getOrElse($union);

            $candidate = yield Option::fromNullable($event->getReturnTypeCandidate());
            $generic_object = yield Psalm::getUnionSingleAtomicOf($candidate, TGenericObject::class);
            $generic_object->addIntersectionType(new TGenericObject(StaticStorage::class, [$storage]));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterPairCollector(string $method_name, Union $storage): Option
    {
        return Option::some($method_name)
            ->filter(fn($method) => in_array($method, [
                self::COLLECT_PAIRS,
                self::COLLECT_PAIRS_UNSAFE,
                self::COLLECT_PAIRS_NON_EMPTY,
            ]))
            ->flatMap(fn() => self::convertPairsToDict($storage));
    }

    /**
     * @psalm-return Option<Union>
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

            return new Union([new TKeyedArray(yield proveNonEmptyArrayOf($properties, Union::class))]);
        });
    }
}
