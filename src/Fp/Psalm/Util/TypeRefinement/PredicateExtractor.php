<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeRefinement;

use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Node\Expr\VirtualArrowFunction;
use Psalm\Node\Expr\VirtualFuncCall;
use Psalm\Node\Expr\VirtualMethodCall;
use Psalm\Node\Expr\VirtualStaticCall;
use Psalm\Node\Expr\VirtualVariable;
use Psalm\Node\VirtualArg;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Storage\FunctionLikeStorage;
use Psalm\Type\Atomic\TNamedObject;

use function Fp\Callable\ctor;
use function Fp\Collection\first;
use function Fp\Collection\map;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\proveNonEmptyString;
use function Fp\Evidence\proveOf;
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
        return proveTrue($event->getMethodNameLowercase() === strtolower('filterNotNull'))
            ->map(fn() => new VirtualVariable('elem'))
            ->map(fn(VirtualVariable $var) => new VirtualArrowFunction([
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
            ? [new VirtualVariable('key'), new VirtualVariable('val')]
            : [new VirtualVariable('val')];

        return first($event->getCallArgs())
            ->map(fn(Arg $arg) => $arg->value)
            ->filterOf([FuncCall::class, MethodCall::class, StaticCall::class])
            ->filter(fn(CallLike $call_like) => $call_like->isFirstClassCallable())
            ->flatMap(fn(CallLike $call_like) => self::createVirtualCall($call_like, $variables, $event))
            ->map(fn(CallLike $call_like) => new VirtualArrowFunction([
                'expr' => $call_like,
                'params' => map($variables, ctor(Param::class)),
            ]));
    }

    /**
     * @param FuncCall|MethodCall|StaticCall $call_like
     * @param non-empty-list<VirtualVariable> $variables
     * @return Option<CallLike>
     */
    private static function createVirtualCall(CallLike $call_like, array $variables, MethodReturnTypeProviderEvent $event): Option
    {
        $function_id = match (true) {
            $call_like instanceof FuncCall => Option::fromNullable($call_like->name->getAttribute('resolvedName'))
                ->orElse(fn() => proveOf($call_like->name, Name::class)->map(fn(Name $name) => $name->toString()))
                ->flatMap(proveNonEmptyString(...)),
            $call_like instanceof MethodCall => sequenceOptionT(
                PsalmApi::$types->getType($event->getSource(), $call_like->var)
                    ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                    ->filterOf(TNamedObject::class)
                    ->map(fn(TNamedObject $object) => $object->value),
                proveOf($call_like->name, Identifier::class)
                    ->map(fn(Identifier $id) => $id->toString())
                    ->map(strtolower(...)),
            )->mapN(self::toMethodId(...)),
            $call_like instanceof StaticCall => sequenceOptionT(
                proveOf($call_like->class, Name::class)
                    ->map(fn(Name $id) => $id->toString())
                    ->map(strtolower(...))
                    ->map(fn($name) => in_array($name, ['self', 'static', 'parent']) ? $event->getContext()->self : $name)
                    ->flatMap(proveNonEmptyString(...)),
                proveOf($call_like->name, Identifier::class)
                    ->map(fn(Identifier $id) => $id->toString())
                    ->map(strtolower(...)),
            )->mapN(self::toMethodId(...)),
        };

        $args = map($variables, ctor(VirtualArg::class));

        return $function_id->flatMap(
            fn($id) => self::withCustomAssertions($id, $event->getSource(), match (true) {
                $call_like instanceof FuncCall => new VirtualFuncCall($call_like->name, $args),
                $call_like instanceof MethodCall => new VirtualMethodCall($call_like->var, $call_like->name, $args),
                $call_like instanceof StaticCall => new VirtualStaticCall($call_like->class, $call_like->name, $args),
            })
        );
    }

    /**
     * @return non-empty-string
     */
    private static function toMethodId(string $class_name, string $method_name): string
    {
        return "{$class_name}::{$method_name}";
    }

    /**
     * @param non-empty-string $function_id
     * @param VirtualStaticCall|VirtualFuncCall|VirtualMethodCall $expr
     * @return Option<CallLike>
     */
    private static function withCustomAssertions(string $function_id, StatementsSource $source, CallLike $expr): Option
    {
        return proveOf($source, StatementsAnalyzer::class)
            ->flatMap(fn(StatementsAnalyzer $analyzer) => Option
                ::when(
                    cond: PsalmApi::$codebase->functions->functionExists($analyzer, strtolower($function_id)),
                    some: fn() => PsalmApi::$codebase->getFunctionLikeStorage($analyzer, $function_id),
                )
                ->tap(fn(FunctionLikeStorage $storage) => $analyzer->node_data->setIfTrueAssertions($expr, $storage->if_true_assertions))
                ->tap(fn(FunctionLikeStorage $storage) => $analyzer->node_data->setIfFalseAssertions($expr, $storage->if_false_assertions))
                ->map(fn() => $expr));
    }
}
