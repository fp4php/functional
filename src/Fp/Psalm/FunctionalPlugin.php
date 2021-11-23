<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Psalm\Hook\AfterExpressionAnalysis\ConditionallyPureAnalyzer;
use Fp\Psalm\Hook\AfterExpressionAnalysis\StaticStorageRefinementAnalyzer;
use Fp\Psalm\Hook\AfterExpressionAnalysis\ProveTrueExpressionAnalyzer;
use Fp\Psalm\Hook\AfterExpressionAnalysis\UnusedCallAnalyzer;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\EitherAssertionAnalyzer;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\OptionAssertionAnalyzer;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\StaticStorageCollectorAnalyzer;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\ValidatedAssertionAnalyzer;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\FilterFunctionReturnTypeProvider;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartialFunctionReturnTypeProvider;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartitionFunctionReturnTypeProvider;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartitionOfFunctionReturnTypeProvider;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\PluckFunctionReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\CollectionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\MapGetMethodReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\OptionFilterMethodReturnTypeProvider;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

/**
 * Plugin entrypoint
 */
class FunctionalPlugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $register =
            /**
             * @param class-string $hook
             */
            function(string $hook) use ($registration): void {
                class_exists($hook);
                $registration->registerHooksFromClass($hook);
            };

        $register(FilterFunctionReturnTypeProvider::class);
        $register(PartialFunctionReturnTypeProvider::class);
        $register(PartitionFunctionReturnTypeProvider::class);
        $register(PartitionOfFunctionReturnTypeProvider::class);
        $register(PluckFunctionReturnTypeProvider::class);

        $register(CollectionFilterMethodReturnTypeProvider::class);
        $register(OptionFilterMethodReturnTypeProvider::class);
        $register(MapGetMethodReturnTypeProvider::class);

        $register(OptionAssertionAnalyzer::class);
        $register(EitherAssertionAnalyzer::class);
        $register(ValidatedAssertionAnalyzer::class);
        $register(ProveTrueExpressionAnalyzer::class);
        $register(ConditionallyPureAnalyzer::class);
        $register(UnusedCallAnalyzer::class);
        $register(StaticStorageCollectorAnalyzer::class);
        $register(StaticStorageRefinementAnalyzer::class);
    }
}
