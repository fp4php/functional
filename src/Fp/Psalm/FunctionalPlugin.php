<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Psalm\PluginHook\FilterFunctionReturnTypeProvider;
use Fp\Psalm\PluginHook\OptionGetOrElseMethodReturnTypeProvider;
use Fp\Psalm\PluginHook\PartialFunctionReturnTypeProvider;
use Fp\Psalm\PluginHook\PartitionFunctionReturnTypeProvider;
use Fp\Psalm\PluginHook\PluckFunctionReturnTypeProvider;
use Fp\Psalm\PluginHook\ProveTrueExpressionAnalyser;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

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
        $register(PluckFunctionReturnTypeProvider::class);
        $register(OptionGetOrElseMethodReturnTypeProvider::class);
        $register(ProveTrueExpressionAnalyser::class);
    }
}
