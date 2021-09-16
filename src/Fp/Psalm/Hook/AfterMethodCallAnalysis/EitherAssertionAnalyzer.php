<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterMethodCallAnalysis;

use Fp\Psalm\Util\TypeAssertion\EitherToUnion;
use Fp\Psalm\Util\TypeAssertion\TypeAssertion;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;

final class EitherAssertionAnalyzer implements AfterMethodCallAnalysisInterface
{
    private const ASSERTION_METHODS = ['isLeft', 'isRight'];

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        TypeAssertion::changeTypeAfterAssertionCall($event, new EitherToUnion(), self::ASSERTION_METHODS);
    }
}
