<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Yield_;
use Psalm\Node\Expr\VirtualFuncCall;
use Psalm\Node\Name\VirtualFullyQualified;
use Psalm\Internal\Analyzer\ClosureAnalyzer;
use Psalm\Internal\Analyzer\Statements\Expression\Call\FunctionCallAnalyzer;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class ProveTrueExpressionAnalysis implements AfterExpressionAnalysisInterface, PluginEntryPointInterface
{
    public static function getFunctionIds(): array
    {
        return ['fp\evidence\provetrue'];
    }

    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }

    /**
     * @psalm-suppress InternalClass, InternalMethod
     */
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        return Option::do(function() use ($event) {
            $statements_source = $event->getStatementsSource();

            yield proveOf($statements_source->getSource(), ClosureAnalyzer::class);
            $statements_analyzer = yield proveOf($statements_source, StatementsAnalyzer::class);

            $yield = yield proveOf($event->getExpr(), Yield_::class);
            $func_call = yield proveOf($yield->value, FuncCall::class);

            yield proveTrue('Fp\Evidence\proveTrue' === $func_call->name->getAttribute('resolvedName'));

            FunctionCallAnalyzer::analyze(
                $statements_analyzer,
                new VirtualFuncCall(
                    new VirtualFullyQualified('assert'),
                    $func_call->args,
                ),
                $event->getContext(),
            );
        })
        ->get();
    }
}
