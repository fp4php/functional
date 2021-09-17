<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterMethodCallAnalysis;

use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\Set;
use Fp\Collections\StaticStorage;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Type\Atomic\TGenericObject;

use function Fp\Collection\head;
use function Fp\Evidence\proveTrue;

final class StaticStorageCollectorAnalyzer implements AfterMethodCallAnalysisInterface
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
            $candidate = yield Option::fromNullable($event->getReturnTypeCandidate());
            $generic_object = yield Psalm::getUnionSingleAtomicOf($candidate, TGenericObject::class);
            $generic_object->addIntersectionType(new TGenericObject(StaticStorage::class, [$union]));
        });
    }
}
