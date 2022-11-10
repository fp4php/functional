<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeRefinement;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Param;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Node\Expr\VirtualArrowFunction;
use Psalm\Node\Expr\VirtualFuncCall;
use Psalm\Node\Expr\VirtualMethodCall;
use Psalm\Node\Expr\VirtualStaticCall;
use Psalm\Node\VirtualArg;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;

use Psalm\Storage\Assertion;
use function Fp\Callable\ctor;
use function Fp\Collection\first;
use function Fp\Collection\map;
use function Fp\Evidence\proveTrue;

final class PredicateExtractor
{
    /**
     * @psalm-return Option<FunctionLike>
     */
    public static function extract(MethodReturnTypeProviderEvent $event): Option
    {
        return first($event->getCallArgs())
            ->map(fn(Arg $arg) => $arg->value)
            ->filterOf([Closure::class, ArrowFunction::class])
            ->orElse(fn() => self::mockNotNullPredicateArg($event))
            ->orElse(fn() => self::mockFirstClassCallable($event));
    }

    /**
     * @psalm-return Option<VirtualArrowFunction>
     */
    private static function mockNotNullPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return proveTrue(strtolower('filterNotNull') === $event->getMethodNameLowercase())
            ->map(fn() => new Variable('elem'))
            ->map(fn(Variable $var) => new VirtualArrowFunction([
                'expr' => new Isset_([$var]),
                'params' => [new Param($var)],
            ]));
    }

    /**
     * @psalm-return Option<VirtualArrowFunction>
     */
    private static function mockFirstClassCallable(MethodReturnTypeProviderEvent $event): Option
    {
        $variables = $event->getMethodNameLowercase() === strtolower('filterKV')
            ? [new Variable('key'), new Variable('val')]
            : [new Variable('val')];

        // todo: Only function call works.
        //  instance/static method call is not ready
        return first($event->getCallArgs())
            ->map(fn(Arg $arg) => $arg->value)
            ->filterOf(CallLike::class)
            ->filter(fn(CallLike $call_like) => $call_like->isFirstClassCallable())
            ->flatMap(function(CallLike $call_like) use ($variables, $event) {
                $args = ArrayList::collect($variables)
                    ->map(ctor(VirtualArg::class))
                    ->toList();

                return Option::fromNullable(match (true) {
                    $call_like instanceof FuncCall => new VirtualFuncCall($call_like->name, $args),
                    $call_like instanceof MethodCall => (function() use ($call_like, $args, $event) {
                        $call = new VirtualMethodCall(
                            var: $call_like->var,
                            name: $call_like->name,
                            args: $args,
                        );

                        $source = $event->getSource();

                        if ($source instanceof StatementsAnalyzer) {
                            $assertions = [
                                new Assertion(0, [['int']]),
                            ];

                            $source->node_data->setIfTrueAssertions($call, $assertions);
                        }

                        return $call;
                    })(),
                    $call_like instanceof StaticCall => (function() use ($call_like, $args, $event) {
                        $call = new VirtualStaticCall($call_like->class, $call_like->name, $args);

                        $source = $event->getSource();

                        if ($source instanceof StatementsAnalyzer) {
                            $assertions = [
                                new Assertion(0, [['int']]),
                            ];

                            $source->node_data->setIfTrueAssertions($call, $assertions);
                        }

                        return $call;
                    })(),
                    default => null,
                });
            })
            ->map(function(CallLike $call_like) use ($variables) {
                return new VirtualArrowFunction([
                    'expr' => $call_like,
                    'params' => map($variables, ctor(Param::class)),
                ]);
            });
    }
}
