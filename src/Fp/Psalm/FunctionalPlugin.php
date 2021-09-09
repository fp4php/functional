<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Psalm\Hook\ConditionallyPureAnalysis;
use Fp\Psalm\Hook\EitherAssertionAnalysis;
use Fp\Psalm\Hook\FilterFunctionReturnTypeProvider;
use Fp\Psalm\Hook\CollectionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hook\MapGetMethodReturnTypeProvider;
use Fp\Psalm\Hook\OptionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hook\OptionAssertionAnalysis;
use Fp\Psalm\Hook\PartialFunctionReturnTypeProvider;
use Fp\Psalm\Hook\PartitionFunctionReturnTypeProvider;
use Fp\Psalm\Hook\PartitionOfFunctionReturnTypeProvider;
use Fp\Psalm\Hook\PluckFunctionReturnTypeProvider;
use Fp\Psalm\Hook\ProveTrueExpressionAnalyser;
use Fp\Psalm\Hook\ValidatedAssertionAnalysis;
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
        $register(OptionAssertionAnalysis::class);
        $register(EitherAssertionAnalysis::class);
        $register(ValidatedAssertionAnalysis::class);
        $register(ProveTrueExpressionAnalyser::class);
        $register(MapGetMethodReturnTypeProvider::class);
        $register(ConditionallyPureAnalysis::class);
    }
}
