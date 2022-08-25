<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterExpressionAnalysis;

use Fp\Functional\Option\Option;
use PhpParser\Node;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Psalm\Node\Expr\VirtualFuncCall;
use Psalm\Node\Name\VirtualFullyQualified;
use Psalm\Internal\Analyzer\ClosureAnalyzer;
use Psalm\Internal\Analyzer\Statements\Expression\Call\FunctionCallAnalyzer;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveOf;

final class ProveTrueExpressionAnalyzer implements AfterExpressionAnalysisInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Evidence\proveTrue')];
    }

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        sequenceOption([
            fn() => Option::some($event->getExpr())
                ->filterOf(Yield_::class)
                ->flatMap(self::getProveTrueArgsFromYield(...)),
            fn() => proveOf($event->getStatementsSource(), StatementsAnalyzer::class)
                ->filter(fn(StatementsAnalyzer $source) => $source->getSource() instanceof ClosureAnalyzer),
            fn() => Option::some($event),
        ])->tapN(self::assert(...));

        return null;
    }

    /**
     * @param Node\Arg[] $prove_true_args
     */
    private static function assert(
        array $prove_true_args,
        StatementsAnalyzer $analyzer,
        AfterExpressionAnalysisEvent $event,
    ): void {
        FunctionCallAnalyzer::analyze(
            $analyzer,
            new VirtualFuncCall(
                new VirtualFullyQualified('assert'),
                $prove_true_args,
            ),
            $event->getContext(),
        );
    }

    /**
     * @psalm-return Option<Node\Arg[]>
     */
    private static function getProveTrueArgsFromYield(Yield_ $expr): Option
    {
        $visitor = new class extends NodeVisitorAbstract {
            /** @var Node\Arg[] */
            public array $proveTrueArgs = [];

            public function leaveNode(Node $node): ?int
            {
                $this->proveTrueArgs = Option::some($node)
                    ->filterOf(FuncCall::class)
                    ->filter(fn(FuncCall $n) => 'Fp\Evidence\proveTrue' === $n->name->getAttribute('resolvedName'))
                    ->filter(fn(FuncCall $n) => !$n->isFirstClassCallable())
                    ->map(fn(FuncCall $n) => $n->getArgs())
                    ->getOrElse([]);

                return !empty($this->proveTrueArgs)
                    ? NodeTraverser::STOP_TRAVERSAL
                    : null;
            }
        };

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$expr]);

        return proveNonEmptyArray($visitor->proveTrueArgs);
    }
}
