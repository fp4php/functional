<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\Analysis;

use Fp\Psalm\Util\TypeAssertion\OptionToUnion;
use Fp\Psalm\Util\TypeAssertion\TypeAssertion;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;

final class OptionAssertionAnalysis implements AfterMethodCallAnalysisInterface
{
    private const ASSERTION_METHODS = ['isSome', 'isNone', 'isEmpty', 'isNonEmpty'];

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        TypeAssertion::changeTypeAfterAssertionCall($event, new OptionToUnion(), self::ASSERTION_METHODS);
    }
}
