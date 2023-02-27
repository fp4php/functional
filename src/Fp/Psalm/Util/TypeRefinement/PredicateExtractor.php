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
use Psalm\Internal\MethodIdentifier;
use Psalm\Node\Expr\VirtualArrowFunction;
use Psalm\Node\Expr\VirtualFuncCall;
use Psalm\Node\Expr\VirtualMethodCall;
use Psalm\Node\Expr\VirtualStaticCall;
use Psalm\Node\Expr\VirtualVariable;
use Psalm\Node\VirtualArg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Storage\FunctionLikeStorage;
use Psalm\Type\Atomic\TNamedObject;

use function Fp\Callable\ctor;
use function Fp\Collection\first;
use function Fp\Collection\map;
use function Fp\Collection\second;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\of;
use function Fp\Evidence\proveNonEmptyString;
use function Fp\Evidence\proveTrue;

final class PredicateExtractor
{
    /**
     * @psalm-return Option<FunctionLike>
     */
    public static function extract(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::firstT(
            fn() => self::getPredicateCallback($event),
            fn() => self::mockNotNullPredicateArg($event),
            fn() => self::mockFirstClassCallable($event),
        );
    }

    /**
     * @return Option<Closure|ArrowFunction>
     */
    private static function getPredicateCallback(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        $predicate_arg = $event instanceof MethodReturnTypeProviderEvent
            ? first($event->getCallArgs())
            : second($event->getCallArgs());

        return $predicate_arg
            ->map(fn(Arg $arg) => $arg->value)
            ->flatMap(of([Closure::class, ArrowFunction::class]));
    }

    /**
     * @psalm-return Option<VirtualArrowFunction>
     */
    private static function mockNotNullPredicateArg(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::some($event)
            ->flatMap(of(MethodReturnTypeProviderEvent::class))
            ->filter(fn($e) => $e->getMethodNameLowercase() === strtolower('filterNotNull'))
            ->map(fn() => new VirtualVariable('elem'))
            ->map(fn(VirtualVariable $var) => new VirtualArrowFunction([
                'expr' => new Isset_([$var]),
                'params' => [new Param($var)],
            ]));
    }

    /**
     * @psalm-return Option<VirtualArrowFunction>
     */
    private static function mockFirstClassCallable(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $e): Option
    {
        $calling_id = $e instanceof MethodReturnTypeProviderEvent
            ? $e->getMethodNameLowercase()
            : $e->getFunctionId();

        $variables = strtolower('filterKV') === $calling_id || strtolower('Fp\Collection\filterKV') === $calling_id
            ? [new VirtualVariable('key'), new VirtualVariable('val')]
            : [new VirtualVariable('val')];

        $predicate_arg = $e instanceof MethodReturnTypeProviderEvent
            ? first($e->getCallArgs())
            : second($e->getCallArgs());

        return $predicate_arg
            ->map(fn(Arg $arg) => $arg->value)
            ->flatMap(of([FuncCall::class, MethodCall::class, StaticCall::class]))
            ->filter(fn(CallLike $call_like) => $call_like->isFirstClassCallable())
            ->flatMap(fn(CallLike $call_like) => self::createVirtualCall(
                statements_source: $e instanceof MethodReturnTypeProviderEvent ? $e->getSource() : $e->getStatementsSource(),
                self: $e->getContext()->self,
                original_call: $call_like,
                fake_variables: $variables,
            ))
            ->map(fn(CallLike $call_like) => new VirtualArrowFunction([
                'expr' => $call_like,
                'params' => map($variables, ctor(Param::class)),
            ]));
    }

    /**
     * @param FuncCall|MethodCall|StaticCall $original_call
     * @param non-empty-list<VirtualVariable> $fake_variables
     * @return Option<CallLike>
     */
    private static function createVirtualCall(
        StatementsSource $statements_source,
        null|string $self,
        CallLike $original_call,
        array $fake_variables,
    ): Option {
        $function_id = match (true) {
            $original_call instanceof FuncCall => Option::fromNullable($original_call->name->getAttribute('resolvedName'))
                ->orElse(fn() => Option::some($original_call->name)
                    ->flatMap(of(Name::class))
                    ->map(fn(Name $name) => (string) $name))
                ->flatMap(proveNonEmptyString(...)),
            $original_call instanceof MethodCall => sequenceOptionT(
                PsalmApi::$types->getType($statements_source, $original_call->var)
                    ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                    ->flatMap(of(TNamedObject::class))
                    ->map(fn(TNamedObject $object) => $object->value),
                Option::some($original_call->name)
                    ->flatMap(of(Identifier::class))
                    ->map(fn(Identifier $id) => (string) $id),
            )->mapN(self::toMethodId(...)),
            $original_call instanceof StaticCall => sequenceOptionT(
                Option::some($original_call->class)
                    ->flatMap(of(Name::class))
                    ->map(fn(Name $id) => (string) $id)
                    ->map(fn($name) => in_array($name, ['self', 'static', 'parent']) ? $self : $name)
                    ->flatMap(proveNonEmptyString(...)),
                Option::some($original_call->name)
                    ->flatMap(of(Identifier::class))
                    ->map(fn(Identifier $id) => (string) $id),
            )->mapN(self::toMethodId(...)),
        };

        $args = map($fake_variables, ctor(VirtualArg::class));

        return $function_id->flatMap(
            fn($id) => self::withCustomAssertions($id, $statements_source, match (true) {
                $original_call instanceof FuncCall => new VirtualFuncCall($original_call->name, $args),
                $original_call instanceof MethodCall => new VirtualMethodCall($original_call->var, $original_call->name, $args),
                $original_call instanceof StaticCall => new VirtualStaticCall($original_call->class, $original_call->name, $args),
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
        return Option::some($source)
            ->flatMap(of(StatementsAnalyzer::class))
            ->flatMap(fn(StatementsAnalyzer $analyzer) => Option
                ::when(
                    cond: PsalmApi::$codebase->functions->functionExists($analyzer, strtolower($function_id)),
                    some: fn() => PsalmApi::$codebase->functions->getStorage($analyzer, strtolower($function_id)),
                )
                ->orElse(fn() => Option::when(
                    cond: PsalmApi::$codebase->methods->hasStorage(MethodIdentifier::wrap($function_id)),
                    some: fn() => PsalmApi::$codebase->methods->getStorage(MethodIdentifier::wrap($function_id)),
                ))
                ->tap(fn(FunctionLikeStorage $storage) => $analyzer->node_data->setIfTrueAssertions($expr, $storage->if_true_assertions))
                ->tap(fn(FunctionLikeStorage $storage) => $analyzer->node_data->setIfFalseAssertions($expr, $storage->if_false_assertions))
                ->map(fn() => $expr));
    }
}
