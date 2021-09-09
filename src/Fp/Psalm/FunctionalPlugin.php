<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Psalm\Hook\AfterExpressionAnalysis\ConditionallyPureAnalysis;
use Fp\Psalm\Hook\AfterExpressionAnalysis\MapGetMethodCallAnalysis;
use Fp\Psalm\Hook\AfterExpressionAnalysis\ProveTrueExpressionAnalysis;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\EitherAssertionAnalysis;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\OptionAssertionAnalysis;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\StaticStorageAnalysis;
use Fp\Psalm\Hook\AfterMethodCallAnalysis\ValidatedAssertionAnalysis;
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
        $register = function(string $hook) use ($registration): void {
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

        $register(OptionAssertionAnalysis::class);
        $register(EitherAssertionAnalysis::class);
        $register(ValidatedAssertionAnalysis::class);
        $register(ProveTrueExpressionAnalysis::class);
        $register(ConditionallyPureAnalysis::class);
        $register(StaticStorageAnalysis::class);
        $register(MapGetMethodCallAnalysis::class);
    }
}
