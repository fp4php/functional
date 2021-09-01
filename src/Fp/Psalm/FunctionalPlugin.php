<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Psalm\Hooks\EitherAssertionAnalysis;
use Fp\Psalm\Hooks\FilterFunctionReturnTypeProvider;
use Fp\Psalm\Hooks\CollectionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hooks\MapGetMethodReturnTypeProvider;
use Fp\Psalm\Hooks\OptionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hooks\OptionAssertionAnalysis;
use Fp\Psalm\Hooks\PartialFunctionReturnTypeProvider;
use Fp\Psalm\Hooks\PartitionFunctionReturnTypeProvider;
use Fp\Psalm\Hooks\PartitionOfFunctionReturnTypeProvider;
use Fp\Psalm\Hooks\PluckFunctionReturnTypeProvider;
use Fp\Psalm\Hooks\ProveTrueExpressionAnalyser;
use Fp\Psalm\Hooks\ValidatedAssertionAnalysis;
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
    }
}
