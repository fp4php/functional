<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\Analysis;

use Fp\Psalm\Util\TypeAssertion\TypeAssertion;
use Fp\Psalm\Util\TypeAssertion\ValidatedToUnion;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;

final class ValidatedAssertionAnalysis implements AfterMethodCallAnalysisInterface
{
    private const ASSERTION_METHODS = ['isInvalid', 'isValid'];

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        TypeAssertion::changeTypeAfterAssertionCall($event, new ValidatedToUnion(), self::ASSERTION_METHODS);
    }
}
