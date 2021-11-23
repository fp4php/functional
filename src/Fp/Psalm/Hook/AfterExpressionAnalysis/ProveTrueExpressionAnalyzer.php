<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterExpressionAnalysis;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node;
use PhpParser\Node\Expr\Yield_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Psalm\Node\Expr\VirtualFuncCall;
use Psalm\Node\Name\VirtualFullyQualified;
use Psalm\Internal\Analyzer\ClosureAnalyzer;
use Psalm\Internal\Analyzer\Statements\Expression\Call\FunctionCallAnalyzer;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

use function Fp\Evidence\proveOf;

final class ProveTrueExpressionAnalyzer implements AfterExpressionAnalysisInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Evidence\proveTrue')];
    }

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        return Option::do(function() use ($event) {
            $statements_source = $event->getStatementsSource();

            yield proveOf($statements_source->getSource(), ClosureAnalyzer::class);
            $statements_analyzer = yield proveOf($statements_source, StatementsAnalyzer::class);

            $prove_true_args = yield self::getProveTrueArgsFromYield($event->getExpr());

            FunctionCallAnalyzer::analyze(
                $statements_analyzer,
                new VirtualFuncCall(
                    new VirtualFullyQualified('assert'),
                    $prove_true_args->toArray(),
                ),
                $event->getContext(),
            );
        })
        ->get();
    }

    /**
     * @psalm-return Option<ArrayList<Node\Arg>>
     */
    private static function getProveTrueArgsFromYield(Node\Expr $expr): Option
    {
        if (!($expr instanceof Yield_)) {
            return Option::none();
        }

        $visitor = new class extends NodeVisitorAbstract {
            /**
             * @var null|ArrayList<Node\Arg>
             */
            public ?ArrayList $proveTrueArgs = null;

            public function leaveNode(Node $node): void
            {
                if (null !== $this->proveTrueArgs) {
                    return;
                }

                $this->proveTrueArgs = Option::some($node)
                    ->filter(fn($n) => $n instanceof Node\Expr\FuncCall)
                    ->filter(fn($n) => 'Fp\Evidence\proveTrue' === $n->name->getAttribute('resolvedName'))
                    ->map(fn($n) => Psalm::getCallArgs($n)->get())
                    ->get();
            }
        };

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$expr]);

        return Option::fromNullable($visitor->proveTrueArgs);
    }
}
