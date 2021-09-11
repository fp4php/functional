<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterMethodCallAnalysis;

use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\Set;
use Fp\Collections\StaticStorage;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Type\Atomic\TGenericObject;

use function Fp\Collection\head;
use function Fp\Evidence\proveTrue;

final class StaticStorageAnalysis implements AfterMethodCallAnalysisInterface
{
    public static function getAllowedMethods(): Set
    {
        return HashSet::collect([
            HashMap::class.'::collect',
        ]);
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
